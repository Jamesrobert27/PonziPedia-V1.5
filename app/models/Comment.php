<?php

class Comment extends Model {
	
	protected $table = 'comments';

	protected $votesTable = 'commentvotes';

	protected $guarded = array('id');

	protected $_user;

	protected $_replies;

	protected function getIdAttribute($value)
	{
		return (int) $value;
	}

	protected function getUserIdAttribute($value)
	{
		return (int) $value;
	}

	protected function getStatusAttribute($value)
	{
		return (int) $value;
	}

	protected function getUpVotesAttribute($value)
	{
		return (int) $value;
	}

	protected function getDownVotesAttribute($value)
	{
		return (int) $value;
	}

	protected function getParentAttribute($value)
	{
		return (int) $value;
	}

	protected function getParentCommentAttribute()
	{
		if (empty($this->parent)) {
			return null;
		}

		return $this->find($this->parent);
	}

	protected function getRepliesAttribute()
	{
		if (!isset($this->_replies)) {
			$this->_replies = $this->where('status', 1)
								   ->where('parent', $this->id)
								   ->get();
		}

		return $this->_replies;
	}

	protected function getUserAttribute()
	{
		if (!isset($this->_user)) {
			$this->_user = User::find($this->user_id);
		}

		return $this->_user;
	}

	protected function getVotedAttribute()
	{
		if (Auth::guest() || !$this->user) {
			return null;
		}

		return CommentVote::where('user_id', Auth::user()->id)
						  ->where('comment_id', $this->id)
						  ->pluck('type');
	}

	public function toArray($withReplies = false)
	{
		if (!$user = $this->user) {
			return null;
		}

		$extra = array(
			'user' => array(
				'id'     => $user->id,
				'name'   => $user->display_name,
				'avatar' => $user->avatar,
			),
			'content'  => Comments::formatComment($this->content),
			'_content' => $this->content,
			'auth' => array(
				'edit'  => Comments::canEdit($this),
				'reply' => Comments::canReply($this),
				'vote'  => (int) $this->voted,
				'moderate' => Auth::check() ? Auth::userCan('moderate') : false,
			),
			'date' => with(new DateTime($this->date))->format(DateTime::ISO8601),
			'replies' => array(),
		);

		if ($withReplies) {
			foreach ($this->replies as $comment) {
				$extra['replies'][] = $comment->toArray(true);
			}
		}

		return array_merge(parent::toArray(), $extra);
	}
}