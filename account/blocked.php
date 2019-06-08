<?php

require_once '../app/init.php';
$user_id = Auth::user()->id;
$user = DB::table('users')->where('id', $user_id)->first();
if ($user->status !='2')
  {
  redirect_to(App::url('account/index.php'));
  }


?>
<head>
	
<title>Blocked Account</title>

	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

</head>
<body>
	
<div class="container">
	
	<div class="jumbotron">
	 <h1 style="color:red">Your Account has been blocked</h1>
    </div>
    <center><img src="<?php echo asset_url('img/avatar-56f3a45e06.gif') ?>"></center> 
</div>    
    
</div>
</body> 