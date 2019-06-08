<?php
require_once '../../app/init.php';
if (Auth::check()) redirect_to(App::url('account'));

if (!Auth::check()) redirect_to(App::url('account/auth/login.php'));
?>
