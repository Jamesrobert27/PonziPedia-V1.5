<?php if (!Auth::userCan('message_users')) page_restricted(); ?>

<?php echo View::make('admin.header')->render() ?>
<?php

if (empty($_GET['u']) || !is_numeric($_GET['u'])) {
	redirect_to('?page=support');
}


$userid = $_GET['u'];


if (isset($_POST['MessageSubmit'])) {
	$message  = $_POST['ReplyMessage'];
	ReplyMessageU($userid,$message);
}
?>

<head>
	<style type="text/css">
		.chat
{
    list-style: none;
    margin: 0;
    padding: 0;
}

.chat li
{
    margin-bottom: 10px;
    padding-bottom: 5px;
    border-bottom: 1px dotted #B3A9A9;
}

.chat li.left .chat-body
{
    margin-left: 60px;
}

.chat li.right .chat-body
{
    margin-right: 60px;
}


.chat li .chat-body p
{
    margin: 0;
    color: #777777;
}

.panel .slidedown .glyphicon, .chat .glyphicon
{
    margin-right: 5px;
}

.panel-body
{
    overflow-y: scroll;
    height: 250px;
}

::-webkit-scrollbar-track
{
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
    background-color: #F5F5F5;
}

::-webkit-scrollbar
{
    width: 12px;
    background-color: #F5F5F5;
}

::-webkit-scrollbar-thumb
{
    -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
    background-color: #555;
}

	</style>
	
</head>
 <h3 class="page-header">
Reply Support Message
</h3>
<div class="row">
<div class="col-md-2">
</div>
</div>



        <div class="col-md-10">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <span class="glyphicon glyphicon-comment"></span> Chat
                    <div class="btn-group pull-right">
                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                            <span class="glyphicon glyphicon-chevron-down"></span>
                        </button>
                        <ul class="dropdown-menu slidedown">
                            <li><a href="http://www.jquery2dotnet.com"><span class="glyphicon glyphicon-refresh">
                            </span>Refresh</a></li>
                           
                        </ul>
                    </div>
                </div>
                <div class="panel-body">
                    <ul class="chat">
                       <?php messageUView($userid); ?>
                    </ul>
                </div>
                <form method="POST" action="">
                <div class="panel-footer">
                    <div class="input-group">
                    	
                    	<input id="btn-input" type="text" name="ReplyMessage" class="form-control input-sm" placeholder="Type your message here..." />
                        <span class="input-group-btn">
                            <input type="submit" name="MessageSubmit" class="btn btn-warning btn-sm" id="btn-chat" value="Send" >
                        </span>
                    

                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>





<?php echo View::make('admin.footer')->render() ?>