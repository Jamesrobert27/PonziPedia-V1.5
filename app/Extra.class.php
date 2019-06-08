<?php


//Court Case User view
function CourtCaseView($user_id){
         $settings = DB::table('settings')->where('id', 1)->first();
         $cases = DB::table('courtcase')->where('userid', $user_id)->orWhere('accused', $user_id)->get();
         if ($cases) {
	       echo ' <div class="list-group"> ';
           foreach ($cases as $row) {

           	     	$user = DB::table('userdetails')->where('userid', $row->userid)->first();
     	$AcussedUser = DB::table('userdetails')->where('userid', $row->accused)->first();
     	$margin = DB::table('marching')->where('id', $row->margin_id)->first();
           	if ($row->status ==2) {
           		$status = '<b style="color:yellow;">(OPEN)</b>';
           	}elseif ($row->status ==1) {
           		$status = '<b style="color:Green;">(Investigating)</b>';
           	
           		}elseif ($row->status ==0) {
           		$status = '<b style="color:red;">(resolved)</b>';
           	}else
           	{
           		$status = '<b style="color:yellow;">(OPEN)</b>';
           	}
           	if ($row->userid == $user_id) {
           		$owner ="My case Between " .$AcussedUser->accountname;
           	}else 	if ($row->accused == $user_id) {
           		$owner ="I am accused by " .$user->accountname;
           	}

         echo '<a href="#" class="list-group-item"><blockquote>'.$owner.' | Reason: '.$row->type.' | Amount: '.$settings->currency.' '.$margin->amount.'  </blockquote><hr><p style="color: #000 !important;">Description:<br> '.$row->details.' <h3 style="color: #000 !important;">Status: '.$status.'</h3></p></a>';
     
   }

	echo ' </div>';
}
else
{
	echo '<div class="alert alert-solid bg-success" style="background-color: #4caf50;color: #fff;font-size: 16px;font-weight: 600;border-color: #4caf50;"><center><strong style="font-size:24px !important;">You have no court cases</strong></center><br> Court cases only appear here when you have a payment dispute with another member. Payment Disputes range from "Upline not confirming payment before the given time elapses" to "Downline presenting payment evidence that has been marked as dubious by the upline". When you have disputes of such, your court cases will appear here.</div>';
}
}





function CourtCaseCount($user_id){
   $cases = DB::table('courtcase')->where('userid', $user_id)->orWhere('accused', $user_id)->count();
   if ($cases >0) {
   	echo $cases;
   }else
   {
   	echo "0";
   }
   
	}



function AccusedCaseView($user_id)
{
	$settings = DB::table('settings')->where('id', 1)->first();
	 $cases = DB::table('courtcase')->where('accused', $user_id)->where('status', '!=', 0)->orderBy('id', 'DESC')->get();
	 foreach ($cases as $row) {
	 	$settings = DB::table('settings')->where('id', 1)->first();
	 	$user = DB::table('userdetails')->where('userid', $row->userid)->first();
	 	$marching = DB::table('marching')->where('id', $row->margin_id)->first();
	 	 echo'<div class="alert alert-danger" role="alert">
  You have been reported by '.$user->accountname.' for '.$row->type.' amount of '.$settings->currency.' '.$marching->amount.' to court and the judges are talking bold steps this case, you will get contact you soon or open support ticket to follow up case.
</div>';
	 }
}


function TestimoneyView()
{
   $Testimony = DB::table('testimony')->where('status', 1)->orderBy('id', 'DESC')->get();

   if ($Testimony) {
    
   foreach ($Testimony as $row) {
     $user = DB::table('userdetails')->where('userid', $row->userid)->first();
     echo ' <div class="col-md-6">
                  <div class="card">
                    <div class="card-header text-white bg-danger">Shared by <b>'.$user->accountname.'</b> <span class="pull-right"><a href="?create" data-toggle="tooltip" data-placement="top" title="Write Testimony"><i class="fa fa-pencil-square-o" style="font-size: 30px;color:#fff;"></i></a></span></div>
                    <div class="card-body">
                      <h4 class="card-title">'.$row->Title.'</h4>
                      <p class="card-text">'.$row->content.'</a>
                    </div>
                    <div class="card-footer text-muted">'; 

      $time = $row->date;
       $time_ago = strtotime($row->date);
    $cur_time   = time();
    $time_elapsed   = $cur_time - $time_ago;
    $seconds    = $time_elapsed ;
    $minutes    = round($time_elapsed / 60 );
    $hours      = round($time_elapsed / 3600);
    $days       = round($time_elapsed / 86400 );
    $weeks      = round($time_elapsed / 604800);
    $months     = round($time_elapsed / 2600640 );
    $years      = round($time_elapsed / 31207680 );
    // Seconds
    if($seconds <= 60){
        $PostedTime=  "just now";
    }
    //Minutes
    else if($minutes <=60){
        if($minutes==1){
            $PostedTime=  "one minute ago";
        }
        else{
            $PostedTime=  "$minutes minutes ago";
        }
    }
    //Hours
    else if($hours <=24){
        if($hours==1){
            $PostedTime=  "an hour ago";
        }else{
            $PostedTime=  "$hours hrs ago";
        }
    }
    //Days
    else if($days <= 7){
        if($days==1){
            $PostedTime=  "yesterday";
        }else{
            $PostedTime=  "$days days ago";
        }
    }
    //Weeks
    else if($weeks <= 4.3){
        if($weeks==1){
            $PostedTime=  "a week ago";
        }else{
            $PostedTime=  "$weeks weeks ago";
        }
    }
    //Months
    else if($months <=12){
        if($months==1){
            $PostedTime=  "a month ago";
        }else{
            $PostedTime=  "$months months ago";
        }
    }
    //Years
    else{
        if($years==1){
            $PostedTime=  "one year ago";
        }else{
            $PostedTime=  "$years years ago";
        }
    }
    echo $PostedTime;
    ?>

    <span class="pull-right">  
       <a href="?react=liked&id=<?php echo $row->id; ?>"  data-toggle="tooltip" data-placement="top" title="Like Testimony"> <i class="fa fa-heart text-danger" style="font-size: 30px;"></i></a> <?php $DownVote = DB::table('testimoneytvotes')->where('comment_id', $row->id)->where('type', "liked")->count();
        if ($DownVote) {
           echo $DownVote;
         } else{ echo $row->upvotes; }?>

        <a href="?react=disliked&id=<?php echo $row->id; ?>"  data-toggle="tooltip" data-placement="top" title="Dislike Testimony"><i class="fa fa-thumbs-o-down" style="font-size: 30px;"></i></a> <?php
        $DownVote = DB::table('testimoneytvotes')->where('comment_id', $row->id)->where('type', "disliked")->count();
        if ($DownVote) {
           echo $DownVote;
         } else{ echo $row->downvotes; }?></i>  
        </span>

<?php  

 
 $UsersVoters = DB::table('testimoneytvotes')->where('comment_id', $row->id)->take(6)->get();
 if ($UsersVoters >=1) {
   echo '<hr>
        
       Reactions <span class="pull-right"> ';
       $Counts = DB::table('testimoneytvotes')->where('comment_id', $row->id)->count();
        foreach ($UsersVoters as $key) {  
          $users = DB::table('userdetails')->where('userid', $key->userid)->first();
          if ($users->avater =="") {
             $Userimage = asset_url('img/avatar.png');
            
          }else{
            $Userimage = $users->avater;
          } 
       
     echo ' <a href="#" data-toggle="tooltip" data-placement="top" title="'.$users->accountname.'"><img src="'.$Userimage.'" alt="..." style="width: 30px;" class="img-fluid rounded-circle"></a>';
    } 
    echo  ' <a href="#" data-toggle="tooltip" data-placement="top" title="And '.$Counts.' Others"><img src="'.asset_url('img/menu.png').'" alt="..." style="width: 20px;" class="img-fluid rounded-circle"></a>
</span>';
 }

  
   
                    echo '</div>
                  </div>
                </div>
   
';

 }
}else{

   echo '<div class="col-lg-12">
                  <div class="card">
                   
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4">You have not create any testimony</h3>
                    </div>
                    <div class="card-body text-center">
                      <a href="testimony.php?create" class="btn btn-primary">Create Testimony </a>

                  </div>
                </div>
                </div>';
 }
}


function CreateTestiony($Subject,$message,$user_id,$redirect){

 $sql =  DB::table('testimony')->insert(
    array('userid' => $user_id,
          'Title' => $Subject,
          'content' => $message,
           'status' => 0)
);

 if ($sql) {
   echo'<div class="alert alert-success" role="alert">
  Your have succesfully create testimony and its pending approval by system, Thanks for sharing your thoughts
</div>';

if ($redirect != "") {
   echo '<meta http-equiv="refresh" content="0;url='.$redirect.'&validation=verified"/>';
}
 }
}




function MyTestimoneyView($user_id)
{
   $Testimony = DB::table('testimony')->where('userid', $user_id)->where('status', 1)->orderBy('id', 'DESC')->get();
   if ($Testimony >=1) {
    
 
   foreach ($Testimony as $row) {
     $user = DB::table('userdetails')->where('userid', $row->userid)->first();
     echo ' <div class="col-md-6">
                  <div class="card">
                    <div class="card-header text-white bg-danger">Shared by <b>'.$user->accountname.'</b> <span class="pull-right"><a href="?create" data-toggle="tooltip" data-placement="top" title="Write Testimony"><i class="fa fa-pencil-square-o" style="font-size: 30px;color:#fff;"></i></a></span></div>
                    <div class="card-body">
                      <h4 class="card-title">'.$row->Title.'</h4>
                      <p class="card-text">'.$row->content.'</a>
                    </div>
                    <div class="card-footer text-muted">'; 

      $time = $row->date;
       $time_ago = strtotime($row->date);
    $cur_time   = time();
    $time_elapsed   = $cur_time - $time_ago;
    $seconds    = $time_elapsed ;
    $minutes    = round($time_elapsed / 60 );
    $hours      = round($time_elapsed / 3600);
    $days       = round($time_elapsed / 86400 );
    $weeks      = round($time_elapsed / 604800);
    $months     = round($time_elapsed / 2600640 );
    $years      = round($time_elapsed / 31207680 );
    // Seconds
    if($seconds <= 60){
        $PostedTime=  "just now";
    }
    //Minutes
    else if($minutes <=60){
        if($minutes==1){
            $PostedTime=  "one minute ago";
        }
        else{
            $PostedTime=  "$minutes minutes ago";
        }
    }
    //Hours
    else if($hours <=24){
        if($hours==1){
            $PostedTime=  "an hour ago";
        }else{
            $PostedTime=  "$hours hrs ago";
        }
    }
    //Days
    else if($days <= 7){
        if($days==1){
            $PostedTime=  "yesterday";
        }else{
            $PostedTime=  "$days days ago";
        }
    }
    //Weeks
    else if($weeks <= 4.3){
        if($weeks==1){
            $PostedTime=  "a week ago";
        }else{
            $PostedTime=  "$weeks weeks ago";
        }
    }
    //Months
    else if($months <=12){
        if($months==1){
            $PostedTime=  "a month ago";
        }else{
            $PostedTime=  "$months months ago";
        }
    }
    //Years
    else{
        if($years==1){
            $PostedTime=  "one year ago";
        }else{
            $PostedTime=  "$years years ago";
        }
    }
    echo $PostedTime;
    ?>

    <span class="pull-right">  
       <a href="?react=liked&id=<?php echo $row->id; ?>"  data-toggle="tooltip" data-placement="top" title="Like Testimony"> <i class="fa fa-heart text-danger" style="font-size: 30px;"></i></a> <?php $DownVote = DB::table('testimoneytvotes')->where('comment_id', $row->id)->where('type', "liked")->count();
        if ($DownVote) {
           echo $DownVote;
         } else{ echo $row->upvotes; }?>

        <a href="?react=disliked&id=<?php echo $row->id; ?>"  data-toggle="tooltip" data-placement="top" title="Dislike Testimony"><i class="fa fa-thumbs-o-down" style="font-size: 30px;"></i></a> <?php
        $DownVote = DB::table('testimoneytvotes')->where('comment_id', $row->id)->where('type', "disliked")->count();
        if ($DownVote) {
           echo $DownVote;
         } else{ echo $row->downvotes; }?></i> 
        </span>

<?php  

 $UsersVoters = DB::table('testimoneytvotes')->where('comment_id', $row->id)->get();
 if ($UsersVoters >=1) {
   echo '<hr>
        
       Reactions <span class="pull-right"> ';
        foreach ($UsersVoters as $key) {
          $users = DB::table('userdetails')->where('userid', $key->userid)->first();
     echo ' <a href="#" data-toggle="tooltip" data-placement="top" title="'.$users->accountname.'"><i class="fa fa-user-secret" style="font-size: 30px;"></i></a>';
    }
    echo '</span>';
 }

  
   
                    echo '</div>
                  </div>
                </div>
   
';

}
}
 
else{

   echo '<div class="col-lg-12">
                  <div class="card">
                   
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4">You have not create any testimony</h3>
                    </div>
                    <div class="card-body text-center">
                      <a href="testimony.php?create" class="btn btn-primary">Create Testimony </a>

                  </div>
                </div>
                </div>';
 }
}

?>