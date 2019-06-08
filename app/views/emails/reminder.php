<?php 

// Inline page url
$url = App::url("account/auth/reset.php?reminder={$reminder}");

// Modal url
//$url = App::url("#reset-{$reminder}");

$message = trans('emails.reminder_message');
$message .=  '<p><a href="'.$url.'" style="color:#fff;text-decoration:none;text-align:center;display:inline-block;border-radius:2px;background-color:#348eda;padding:8px 10px;border:1px solid #348eda">'.trans('emails.reset_password').'</a></p>';

echo View::make('emails.template')->with('message', $message)->render();