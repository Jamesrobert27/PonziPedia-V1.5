<?php namespace Hazzard\Comments;

use DateTime;
use Hazzard\Auth\Auth;
use Hazzard\Mail\Mailer;
use Hazzard\Support\Kses;
use Hazzard\Events\Dispatcher;
use Hazzard\Support\MessageBag;
use Hazzard\Validation\Factory;

class Comments {

	/**
	 * Moderator permission name.
	 */
	const PERMISSION = 'moderate';

	/**
	 * The comment model name.
	 *
	 * @var string
	 */
	protected $model = 'Comment';

	/**
	 * The comment vote model name.
	 *
	 * @var string
	 */
	protected $voteModel = 'CommentVote';
	
	/**
	 * The comments config items.
	 *
	 * @var array
	 */
	protected $config;

	/**
	 * The auth instance.
	 * 
	 * @var \Hazzard\Auth\Auth
	 */
	protected $auth;

	/**
	 * Validation factory instance.
	 * 
	 * @var \Hazzard\Validation\Factory
	 */
	protected $validator;

	/**
	 * The mailer instance.
	 *
	 * @var \Hazzard\Mail\Mailer
	 */
	protected $mailer;

	/**
	 * The event dispatcher instance.
	 *
	 * @var \Hazzard\Events\Dispatcher
	 */
	protected $events;

	/**
	 * Create a new comments instance.
	 *
	 * @param  array  						$config
	 * @param  \Hazzard\Auth\Auth 			$auth
	 * @param  \Hazzard\Validation\Factory  $validator
	 * @return void
	 */
	public function __construct(array $config, Auth $auth, Factory $validator)
	{
		$this->auth = $auth;

		$this->config = $config;

		$this->validator = $validator;
	}

	/**
	 * Add new comment.
	 *
	 * @param  array $data
	 * @return array|false|\Hazzard\Support\MessageBag
	 */
	public function addComment($data)
	{	
		$data['content'] = isset($data['content'])  ? trim($data['content'])   : '';
		$data['user_id'] = isset($data['user_id'])  ? (int) $data['user_id']   : 0;
		$data['parent']  = isset($data['parent'])   ? absint($data['parent'])  : 0;
		$data['page']    = isset($data['page'])     ? escape($data['page'])    : '';
		$data['page_url']   = isset($data['page_url'])    ? escape($data['page_url'])   : '';
		$data['page_title'] = isset($data['page_title'])  ? escape($data['page_title']) : '';
		$data['date'] 	 = with(new DateTime)->format('Y-m-d H:i:s');

		$data['_content'] = trim($data['content']); // Original content
		$data['content']  = $this->parse($data['content']);

		if (empty($data['user_id'])) {
			$data['user_id'] = $this->user()->id;
		}

		$validator = $this->validateData($data);
		if ($validator->fails() || empty($data['content'])) {
			return $validator->errors();
		}

		$data['status'] = $this->commentStatus($data['user_id'], $data['content']);
		unset($data['_content']);

		if ($data['parent']) {
			if ($comment = $this->model()->find($data['parent'])) {
				if ($comment->status != 1 || !$this->canReply($comment)) {
					return new MessageBag(array('error' => trans('comments.reply_error')));
				}
			} else return false;
		}

		// Fire "comments.add" event.
		if (isset($this->events)) {
			$r = $this->events->fire('comments.add', array($data));
			if ($r === false || is_object($r)) {
				return $r;
			}
		}

		if (!$id = $this->model()->insertGetId($data)) {
			return false;
		}

		$comment = $this->model()->find($id);

		$this->notify($comment);

		return $comment->toArray();
	}

	/**
	 * Update comment.
	 *
	 * @param  int   $id
	 * @param  array $data
	 * @return array|false|\Hazzard\Support\MessageBag
	 */
	public function updateComment($id, array $data)
	{
		if (!$comment = $this->model()->find($id)) {
			return false;
		}

		$data = array_merge($data, array(
			'id' 	  => $comment->id,
			'date'    => $comment->date,
			'page'    => $comment->page,
			'status'  => $comment->status,
			'user_id' => $this->user()->id,
			'content' => isset($data['content']) ? trim($data['content']) : '',
		));

		$data['_content'] = $data['content']; // Original content
		$data['content']  = $this->parse($data['content']);

		if (!$this->canEdit($comment)) {
			return new MessageBag(array('error' => trans('comments.edit_error')));
		}

		$validator = $this->validateData($data);
		if ($validator->fails() || empty($data['content'])) {
			return $validator->errors();
		}

		$comment->content = $data['content'];
		$comment->updated = with(new DateTime)->format('Y-m-d H:i:s');
		
		if ($comment->status == 1) {
			$comment->status = $this->commentStatus($comment->user_id, $comment->content);
		}

		$comment->save();

		return $comment->toArray();
	}

	/**
	 * Get comments.
	 *
	 * @param  array $options
	 * @return array
	 */
	public function getComments(array $options = array())
	{
		$query = $this->model()->newQuery();

		if (isset($options['page'])) {
			$query->where('page', $options['page']);
		}

		if (isset($options['parent'])) {
			$query->where('parent', $options['parent']);
		}

		if (isset($options['status'])) {
			if ($this->user()) {
				$status = $options['status'];
				$userId = $this->user()->id;
				
				$query->where(function($q) use ($status, $userId) {
					$q->where('status', $status);
					$q->orWhere(function($q) use ($status, $userId) {
						$q->whereIn('status', array($status, 0));
						$q->where('user_id', $userId);
					});
				});
			} else {
				$query->where('status', $options['status']);
			}
		}

		if (isset($options['skip']))  {
			$query->skip($options['skip']);
		}

		if ($this->config['per_page']) {
			$query->take($this->config['per_page']);
		}

		if (!isset($options['sort'])) {
			$options['sort'] = $this->config['default_sort'];
		}

		switch ($options['sort']) {
			case 1: $query->orderBy('date', 'desc');     break; // newest
			case 2: $query->orderBy('date', 'asc');      break; // oldest
			case 3: $query->orderBy('upvotes', 'desc');  break; // best
		}

		$withReplies = isset($options['replies']) ? $options['replies'] : false;

		$comments = $query->get();

		if (isset($options['linked'])) {
			$comments = $this->findLinkedComment($options['linked'], $comments);
		}

		foreach ($comments as $key => $comment) {
			if ($comment = $comment->toArray($withReplies)) {
				$comments[$key] = $comment;
			} else {
				unset($comments[$key]);
			}
		}

		return $comments;
	}

	/**
	 * Count pending comments.
	 * 
	 * @return int
	 */
	public function countPending()
	{
		return $this->countComments(0);
	}

	/**
	 * Count approved comments.
	 * 
	 * @return int
	 */
	public function countApproved()
	{
		return $this->countComments(1);
	}

	/**
	 * Count trash comments.
	 * 
	 * @return int
	 */
	public function countTrash()
	{
		return $this->countComments(2);
	}

	/**
	 * Count comments by status.
	 *
	 * @param  int $status
	 * @return int
	 */
	public function countComments($status)
	{
		return $this->model()->where('status', $status)->count('id');
	}

	/**
	 * Unapprove comment(s).
	 * 
	 * @param  int|array $id
	 * @return bool
	 */
	public function unapproveComment($id)
	{
		return $this->commentAction('unapprove', $id);
	}

	/**
	 * Approve comment(s).
	 * 
	 * @param  int|array $id
	 * @return bool
	 */
	public function approveComment($id)
	{
		return $this->commentAction('approve', $id);
	}

	/**
	 * Trash comment(s).
	 * 
	 * @param  int|array $id
	 * @return bool
	 */
	public function trashComment($id)
	{
		return $this->commentAction('trash', $id);
	}

	/**
	 * Delete comment(s).
	 * 
	 * @param  int|array $id
	 * @return bool
	 */
	public function deleteComment($id)
	{
		return $this->commentAction('delete', $id);
	}

	/**
	 * Apply comment action: "unapprove", "approve", "trash", "delete".
	 * 
	 * @param  string    $action
	 * @param  int|array $id
	 * @return bool|void
	 */
	public function commentAction($action, $id)
	{
		if (!$this->user() || !$this->user()->can(self::PERMISSION)) {
			return false;
		}

		if (is_array($id)) {
			foreach ($id as $value) {
				$this->commentAction($action, $value);
			}

			return;
		}

		if (!$comment = $this->model()->find($id)) {
			return false;
		}

		$query = $this->model()->where('parent', $comment->id);

		if ($action == 'delete') {
			if ($comment->delete()) {
				// Delete votes
				$this->voteModel()->where('comment_id', $comment->id)->delete();

				foreach ($query->get() as $comment) {
					$this->commentAction($action, $comment->id);
				}

				return true;
			}
		} else {
			$status = array(
				'unapprove' => 0, 
				'approve' 	=> 1,
				'trash'		=> 2,
			);

			if (isset($status[$action])) {
				$comment->status = $status[$action];

				foreach ($query->get() as $_comment) {
					$this->commentAction($action, $_comment->id);
				}
			}

			return $comment->save();
		}

		return false;
	}

	/**
	 * Delete comments by user id.
	 * 
	 * @param  int|array $id
	 * @return void
	 */
	public function deleteUserComments($id)
	{
		if (!is_array($id)) {
			$id = array($id);
		}
		
		$id = array_values($id);

		$this->model()->whereIn('user_id', $id)->delete();
		$this->voteModel()->whereIn('user_id', $id)->delete();
	}

	/**
	 * Notify admin and comment parent user.
	 * 
	 * @param  \Hazzard\Database\Model $comment
	 * @return void
	 */
	protected function notify(\Hazzard\Database\Model $comment)
	{
		if (!isset($this->mailer)) return;

		// Reply 
		$parentComment = $comment->parentComment;
		if ($parentComment && $user = $parentComment->user) {
			if ($user->id != $this->user()->id && !empty($user->usermeta['email_comments'])) {
				$this->mailer->send('emails.comment', compact('comment'), function($message) use ($user) {
					$message->to($user->email);
					$message->subject(trans('emails.comment_reply_subject'));
				});
			}
		}
	}

	public function find($id, $withReplies = false)
	{
		$comment = $this->model()->find($id);
		
		return $comment ? $comment->toArray($withReplies) : null;
	}

	/**
	 * Find and add the linked comment to the array.
	 * 
	 * @param  int   $id
	 * @param  array $comments
	 * @return array
	 */
	protected function findLinkedComment($id, array $comments)
	{
		$parent = $this->findParent($id);

		if ($comment = $this->model()->find($parent)) {
			if ($comment->status != 1) {
				return $comments;
			}

			foreach ($comments as $key => $_comment) {
				if ($_comment->id == $comment->id) {
					unset($comments[$key]);
				}
			}

			$comments = array_values($comments);
			array_unshift($comments, $comment);
		}

		return $comments;
	}

	/**
	 * Find comment parrent id.
	 * 
	 * @param  int $id
	 * @return int
	 */
	public function findParent($id)
	{
		$comment = $this->model()->find($id);

		if (is_null($comment)) return $id;

		if (empty($comment->parent)) return $comment->id;
		
		return $this->findParent($comment->parent);
	}

	/**
	 * Check whether the user can edit the comment.
	 * 
	 * @param  \Hazzard\Database\Model  $comment
	 * @return bool
	 */
	public function canEdit(\Hazzard\Database\Model $comment)
	{
		// Fire "comments.canEdit" event.
		if (isset($this->events)) {
			if ($this->events->fire('comments.canEdit', array($comment)) === false) {
				return false;
			}
		}

		if (!$this->user()) {
			return false;
		}

		if ($this->user()->can(self::PERMISSION)) {
			return true;
		}

		if ($comment->user_id != $this->user()->id) {
			return false;
		}

		$edit = $this->config['edit'];
		if (is_numeric($edit)) {
			$time = time() - absint($edit);
			return with(new DateTime($comment->date))->getTimestamp() > $time;
		}

		return ($edit === true);
	}

	/**
	 * Check whether the user can reply to the comment.
	 * 
	 * @param  \Hazzard\Database\Model  $comment
	 * @return bool
	 */
	public function canReply(\Hazzard\Database\Model $comment)
	{
		// Fire "comments.canReply" event.
		if (isset($this->events)) {
			if ($this->events->fire('comments.canReply', array($comment)) === false) {
				return false;
			}
		}

		if (!$this->config['replies']) {
			return false;
		}

		return ($this->user()) ? true : false;
	}

	/**
	 * Validate comment data.
	 * 
	 * @param  array $data
	 * @return \Hazzard\Validation\Validator
	 */
	protected function validateData(array $data)
	{
		$maxlength = $this->config['maxlength'];
		$maxlength = $maxlength ? "|max:{$maxlength}" : '';
		$blacklist = implode(',', $this->config['blacklist']);

		// Extend validator to check comment flood and duplicate.
		$me = $this;
		
		$this->validator->extend('flood', function($attr, $data) use ($me) {
			if (isset($data['id'])) return true;
			return !$me->checkCommentFlood($data['user_id'], $data['date']);
		});

		$this->validator->extend('duplicate', function($attr, $data) use ($me) {
			return !$me->checkDuplicateComment($data);
		});

		$this->validator->extend('pending', function($attr, $data) use ($me) {
			return !$me->checkMaxPending();
		});

		return $this->validator->make(
			array(
				'comment' => $data['_content'],
				'user_id' => $data['user_id'],
				'data'	  => $data,
			), 
			array(
				'comment' => "required{$maxlength}",
				'user_id' => "not_in:{$blacklist}|exists:users,id",
				'data'    => 'flood|duplicate|pending',
			),
			array(
				'user_id.not_in' => trans('comments.blocked'),
				'data.flood' 	 => trans('comments.flood'),
				'data.duplicate' => trans('comments.duplicate'),
				'data.pending'   => trans('comments.pending'),
			)
		);
	}

	/**
	 * Check whether comment flooding is occurring.
	 *
	 * @param  int    $userId
	 * @param  string $date
	 * @return bool
	 */
	public function checkCommentFlood($userId, $date)
	{
		if (!$timeBetween = $this->config['time_between']) {
			return false;
		}

		// Don't check users with moderator permission.
		if ($this->user()->can(self::PERMISSION)) {
			return false;
		}

		$hourAgo = with(new DateTime)->setTimestamp(time()-3600)->format('Y-m-d H:i:s');
		
		$lastTime = $this->model()
						 ->where('date', '>=', $hourAgo)
						 ->where('user_id', $userId)
						 ->orderBy('date', 'desc')
						 ->limit(1)
						 ->pluck('date');

		if ($lastTime) {
			return (strtotime($date) - strtotime($lastTime)) < (int) $timeBetween;
		}

		return false;
	}

	/**
	 * Check wheter duplicate comment is detected.
	 * 
	 * @param  array $data
	 * @return bool
	 */
	public function checkDuplicateComment($data)
	{
		// Don't check users with moderator permission.
		if ($this->user()->can(self::PERMISSION)) {
			return false;
		}

		$query = $this->model()
					  ->where('user_id', $data['user_id'])
					  ->where('content', $data['content'])
					  ->where('page',    $data['page'])
					  ->where('status', '!=', 2);
		
		if (isset($data['id'])) {
			$query->where('id', '!=', $data['id']);
		}

		return $query->pluck('id') ? true : false;
	}

	public function checkMaxPending()
	{
		// Don't check users with moderator permission.
		if ($this->user()->can(self::PERMISSION)) {
			return false;
		}

		if ($maxPending = $this->config['max_pending']) {
			$numPending = $this->model()
							   ->where('user_id', $this->user()->id)
							   ->where('status', 0)
							   ->count();
			
			if ($numPending >= $maxPending) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the comment status.
	 * 
	 * @param  int    $userId
	 * @param  string $comment
	 * @return int
	 */
	protected function commentStatus($userId, $comment)
	{	
		$status = 1;

		if ($this->user()->can(self::PERMISSION) || 
								in_array($userId, $this->config['whitelist'])) {
			return $status;
		}

		if ($this->config['moderation']) {
			return 0;
		}

		if ($this->containsRestrictedWords($comment)) {
			$status = 0;
		}

		if ($maxLinks = $this->config['max_links']) {
			$numLinks = preg_match_all('/<a [^>]*href/i', make_clickable($comment), $out);
			
			if ($numLinks >= $maxLinks) {
				$status = 0;
			}
		}

		return $status;
	}

	/**
	 * Check wheter contains restricted words.
	 * 
	 * @param  string $string
	 * @return bool
	 */
	protected function containsRestrictedWords($string)
	{
		foreach ($this->config['restricted_words'] as $word) {
			if (preg_match('/\b'.$word.'\b/', $string)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the authenticated user.
	 * 
	 * @return \Hazzard\Database\Model
	 */
	protected function user()
	{
		return $this->auth->user();
	}

	/**
	 * Escape comment content but allow some html.
	 * 
	 * @param  string $string
	 * @return string
	 */
	public function parse($string)
	{
		$css 	   = $this->config['allowed_css'];
		$tags      = $this->config['allowed_tags'];
		$entities  = $this->config['allowed_entities'];
		$protocols = $this->config['allowed_protocols'];

		if (!$this->config['kses']) {
			return escape($string);
		}
	
		if (is_null($tags) || array_key_exists('pre', $tags)) {
			$string = $this->escapeTag($string, 'pre');
		}

		if (is_null($tags) || array_key_exists('code', $tags)) {
			$string = $this->escapeTag($string, 'code');
		}

		return with(new Kses($tags, $entities, $css, $protocols))->parse($string);
	}

	/**
	 * Escape the content between the given tag.
	 * @param  string $string
	 * @param  string $tag
	 * @return string
	 */
	protected function escapeTag($string, $tag = 'pre')
	{
		$pattern = "(<{$tag}([ ][^>]*|)>((.|\n)*)<\/{$tag}>)";
	
		return preg_replace_callback($pattern, function($matches) use ($tag) {
				return "<{$tag}>".escape($matches[2])."</{$tag}>";
			},
		    $string
		);
	}

	/**
	 * Format comment: make links clickable, add convert smilies.
	 * 
	 * @param  string $comment
	 * @return string
	 */
	public function formatComment($comment)
	{
		$comment = make_clickable($comment);
		
		if ($this->config['use_smilies']) {
			$comment = convert_smilies($comment);
		}

		return $comment;
	}

	/**
	 * Upvote comment.
	 *
	 * @param  int  $id
	 * @param  int  $userId
	 * @return bool
	 */
	public function upVote($id, $userId)
	{

	}

	/**
	 * Downvote comment.
	 *
	 * @param  int  $id
	 * @param  int  $userId
	 * @return bool
	 */
	public function downVote($id, $userId)
	{

	}

	/**
	 * Set the mailer instance.
	 *
	 * @param  \Hazzard\Mail\Mailer
	 * @return self
	 */
	public function setMailer(Mailer $mailer)
	{
		$this->mailer = $mailer;

		return $this;
	}

	/**
	 * Set the event dispatcher instance.
	 *
	 * @param  \Hazzard\Events\Dispatcher
	 * @return self
	 */
	public function setDispatcher(Dispatcher $events)
	{
		$this->events = $events;

		return $this;
	}

	/**
	 * Set comment model name.
	 * 
	 * @param  string $model
	 * @return self
	 */
	public function setModel($model)
	{
		$this->model = $model;

		return $this;
	}

	/**
	 * Create a new instance of the comment vote model.
	 *
	 * @return \Hazzard\Database\Model
	 */
	public function voteModel()
	{
		$class = '\\'.ltrim($this->voteModel, '\\');
		
		return new $class;
	}

	/**
	 * Create a new instance of the comment model.
	 *
	 * @return \Hazzard\Database\Model
	 */
	public function model()
	{
		$class = '\\'.ltrim($this->model, '\\');
		
		return new $class;
	}
}