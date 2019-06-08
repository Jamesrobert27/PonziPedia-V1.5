<?php

$message = trans('emails.new_message', array('message' => $body, 'link' => App::url()));

echo View::make('emails.template')->with('message', $message)->render();