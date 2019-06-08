<?php





function TestimoneyView()
{
   $Testimony = DB::table('testimony')->where('status', 1)->orderBy('id', 'DESC')->take(4)->get();
   foreach ($Testimony as $row) {
     $user = DB::table('userdetails')->where('userid', $row->userid)->first();
     echo ' <div class="col-md-6">
                  <div class="card">
                    <div class="card-header text-white bg-primary">Shared by <b>'.$user->accountname.'</b> <span class="pull-right"><a href="account/testimony.php?create" data-toggle="tooltip" data-placement="top" title="Write Testimony"><i class="fa fa-pencil-square-o" style="font-size: 30px;color:#fff;"></i></a></span></div>
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
       <a href="account/testimony.php?react=liked&id=<?php echo $row->id; ?>"  data-toggle="tooltip" data-placement="top" title="Like Testimony"> <i class="fa fa-heart text-danger" style="font-size: 30px;"></i></a> <?php $DownVote = DB::table('testimoneytvotes')->where('comment_id', $row->id)->where('type', "liked")->count();
        if ($DownVote) {
           echo $DownVote;
         } else{ echo $row->upvotes; }?>

        <a href="account/testimony.php?react=disliked&id=<?php echo $row->id; ?>"  data-toggle="tooltip" data-placement="top" title="Dislike Testimony"><i class="fa fa-thumbs-o-down" style="font-size: 30px;"></i></a> <?php
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

          $users = DB::table('userdetails')->where('userid', $key->userid)->first();
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
}


?>