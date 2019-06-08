<?php

$id   = $comment->id;
$url  = $comment->page_url;
$user = $comment->user->display_name;
$comment = escape($comment->content);

$message =  '<p><b>Comment:</b> '.$comment.'<br><b>User:</b> '.$user.'<br><b>Url:</b> <a href="'.$url.'#comment-'.$id.'">'.$url.'#comment-'.$id.'</a></p>';

echo View::make('emails.template')->with('message', $message)->render();