<?php

echo View::make('emails.template')->with('message', $body)->render();