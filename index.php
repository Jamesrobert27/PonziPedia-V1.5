<?php require_once 'app/init.php';
require_once  'app/testimony.php'; ?>
<?php 

$settings = DB::table('settings')->where('id', 1)->first();
if(isset($_POST['submit'])){

  $emails = $_POST['email'];
$sql = $user = DB::table('subscriber')->where('email', $emails)->first();
  if ($sql) {
    echo   '<div id="top-alert" class="alert alert-danger" role="alert">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>OPPS!</strong> You have already subscribe to our newsletter, please wait for offers mail and promo!
</div>';
  }else{
    DB::table('subscriber')->insert(
    array('email' => $emails)
);
    $to = Config::get('app.webmail');
  $Contactmessage ='I have subscribe to your newsletter and my email is '. $_POST['email'];
    $subject = 'Subscriber notification request from ' .Config::get('app.name');
$header ="From: <".Config::get('app.name').">\r\n" .
    "Reply-To: ".Config::get('app.name')."\r\n"; 
$header .= "MIME-Version: 1.0\r\n";
$header .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
   // if(mail($to,$subject,$Contactmessage,$header)){
if ($emails) {
  
echo  '<div id="top-alert" class="alert alert-success" role="alert">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>Message Sent!</strong>You have subscribe to our newsletter successfully. You will now receive mail with offers and promo!
</div>';
  }
  else {
  echo   '<div id="top-alert" class="alert alert-danger" role="alert">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>Sending Failed</strong> Error while sending your on our system, please Try again Later!
</div>';
  }
}
}
?>
<?php echo View::make('header')->render() ?>


    <section id="hero" class="hero hero-home bg-gray" style="padding-top: 50px;">
      <div class="container">
        <div class="row d-flex">
          <div class="col-lg-6 text order-2 order-lg-1">
            <h1 style="color:  #fff;"><?php echo Config::get('app.name'); ?> &mdash; Trusted&nbsp; and Genuine Returns for&nbsp; Everyone </h1>
            
            <div class="CTA"><a href="account/auth/register.php" class="btn btn-primary btn-shadow btn-gradient link-scroll">Sign Up</a><a href="account/auth/login.php" class="btn btn-outline-primary">Sign In</a></div>
            <p class="hero-text" style="color:  #fff;">A platform that gives all participants an avenue to enjoy a higher standard of living, Our vision is to see people live totally independent lives, free from debt and financial struggles.</p>
          </div>
         
        </div>
      </div>
    </section>
    <section id="browser" class="browser">
      <div class="container">
        <div class="row d-flex justify-content-center"> 
          <div class="col-lg-8 text-center">
            <h2 class="h3 mb-5">How it works</h2>
            <div class="browser-mockup">
              <div id="nav-tabContent" class="tab-content">
                <div id="nav-first" role="tabpanel" aria-labelledby="nav-first-tab" class="tab-pane fade show active"><img src="<?php echo asset_url('frontpage/img/preview-3.png') ?>" alt="..." class="img-fluid"></div>
                <div id="nav-second" role="tabpanel" aria-labelledby="nav-second-tab" class="tab-pane fade"><img src="<?php echo asset_url('frontpage/img/preview-2.png') ?>" alt="..." class="img-fluid"></div>
                <div id="nav-third" role="tabpanel" aria-labelledby="nav-third-tab" class="tab-pane fade"><img src="<?php echo asset_url('frontpage/img/preview-1.png') ?>" alt="..." class="img-fluid"></div>
              </div>
            </div>
          </div>
        </div>
        <div id="myTab" role="tablist" class="nav nav-tabs">
          <div class="row">
            <div class="col-md-4"><a id="nav-first-tab" data-toggle="tab" href="#nav-first" role="tab" aria-controls="nav-first" aria-expanded="true" class="nav-item nav-link active"> <span class="number">1</span>Choose plan to start, We have a range of packages for you to choose from and all package are <?php echo $settings->profit; ?>% ROI (Return Of Investment) after <?php echo $settings->timeMargin; ?>days of investment</a></div>
            <div class="col-md-4"><a id="nav-second-tab" data-toggle="tab" href="#nav-second" role="tab" aria-controls="nav-second" class="nav-item nav-link"> <span class="number">2</span>Get Matched to Pay an Existing Member After <?php echo $settings->ProvideHelpday; ?>
            days of selecting a suitable package </a></div>
            <div class="col-md-4"><a id="nav-third-tab" data-toggle="tab" href="#nav-third" role="tab" aria-controls="nav-third" class="nav-item nav-link"> <span class="number">3</span>While making payment via transfer, mobile or internet banking, ensure that you take screenshots to be uploaded as evidence of payment.</a></div>
          </div>
        </div>
      </div>
    </section>
    <section id="about-us" class="about-us bg-gray">
      <div class="container">
        <h2 style="color:  #fff;">About Us</h2>
        <div class="row">
          <p class="lead col-lg-10">This organization was created by a group of zealous people seeking to promote human welfare and provide a springboard for individuals and corporate organizations alike to thrive via financial empowerment. It is a well known fact that there is a huge gap between the rich and the poor. Our organization aims to significantly reduce that gap with the aim of creating a platform that enables all our members attain a higher standard of living and all round financial freedom. This works by evenly distributing all investments among members to bring everybody up at the same time.<br><br>
                        <h5 style="color:  #fff;">Join our organization today and say goodbye to poverty and financial struggles.</h5></p>
        </div><a href="account/auth/register.php" class="btn btn-primary btn-shadow btn-gradient">Join Now</a>
      </div>
    </section>
   
    <section id="extra-features" class="extra-features bg-primary">
      <div class="container text-center">
        <header>
          <h2>More great features             </h2>
          <div class="row">
            <p class="lead col-lg-8 mx-auto">Auto Recycle, You are free to choose if the system should autorecycle you or not.</p>
          </div>
        </header>
        <div class="grid row">
          <div class="item col-lg-4 col-md-6">
            <div class="icon"> <i class="icon-diploma"></i></div>
            <h3 class="h5">Recommitment Policy </h3>
            <p>All members will automatically recommit 10% of their ROI and will be available on thier next payment.</p>
          </div>
          <div class="item col-lg-4 col-md-6">
            <div class="icon"> <i class="icon-folder-1"></i></div>
            <h3 class="h5">Activation Fees</h3>
            <p>Activation Fees system assist <?php echo Config::get('app.name'); ?> to keep in circle with only serious members. </p>
          </div>
          <div class="item col-lg-4 col-md-6">
            <div class="icon"> <i class="icon-gears"></i></div>
            <h3 class="h5">Periodic Margin</h3>
            <p>Everything on <?php echo Config::get('app.name'); ?> is timed, We give all members ability to know when to payout/paid with countdown timer.</p>
          </div>
          <div class="item col-lg-4 col-md-6">
            <div class="icon"> <i class="icon-management"></i></div>
            <h3 class="h5">Secure System</h3>
            <p>Secured Bcrypt for password hashing, encrypted cookies, We are secured against XSS, SQL injection and CSRF attack preventions.</p>
          </div>
          <div class="item col-lg-4 col-md-6">
            <div class="icon"> <i class="icon-pie-chart"></i></div>
            <h3 class="h5">Mobile Friendly</h3>
            <p><?php echo Config::get('app.name'); ?> is cross-platform system, We are high traffic website, We Speed up our website with our Cache system and relible dedicated hosting server.</p>
          </div>
          <div class="item col-lg-4 col-md-6">
            <div class="icon"> <i class="icon-quality"></i></div>
            <h3 class="h5">Responsive Support</h3>
            <p>Whether you're just starting with Sharegate or are a long time user, our award-winning team will help you solve your issues, and show you how to get the most out of our website 24x7x365.</p>
          </div>
        </div>
      </div>
    </section>
    <section id="testimonials" class="testimonials" style="padding-bottom: 10px;">
      <div class="container">
        <header class="text-center no-margin-bottom">   
          <h2>Happy Clients</h2>
          <p class="lead"> A guest can usually view the contents of our forum or read our member post, 
            <br>but only register user can interact with other members</p>
        </header>
        <div class="row">
          <?php TestimoneyView(); ?>
        </div>
        <hr>
        <div class="text-center"> 
          <div class="item-holder">
            
          <a href="discussion.php" class="btn btn-primary btn-shadow btn-gradient">Join Discussion</a>
          </div>
        
          </div>
        </div>
      </div>
    </section>

    <section id="faq" style="padding-top: 50px;">
  
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2>FAQ</h2>
                    <hr class="star-primary">
                </div>
            </div>
            <div class="row">
                <!-- Start Media Section -->
                <div class="col-sm-12 clearfix media-section">
                    <div class="media">
                        <div class="media-left">
                            <span class="media-rounded">
                               <img src="<?php echo asset_url('img/megaphone.png') ?>" style="margin-right: 10px;">
                            </span>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">What is <? echo Config::get('app.name'); ?>?</h4>
                            <p class="catamaran"><? echo Config::get('app.name'); ?> is a member to member donation and mutual aid fund scheme for members to help other members in an efficient way. By using this scheme, members gives and receives donations from each other.</p>
                        </div>
                    </div>
                </div> <!-- End Media Section -->
                <!-- Start Media Section -->
                <div class="col-sm-12 clearfix media-section">
                    <div class="media">
                        <div class="media-left">
                            <span class="media-rounded">
                                <img src="<?php echo asset_url('img/megaphone.png') ?>" style="margin-right: 10px;">
                            </span>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">What is the aim of <? echo Config::get('app.name'); ?>?</h4>
                            <p class="catamaran">Our aim is to create a platform that gives all participants an avenue to enjoy a higher standard of living.</p>
                        </div>
                    </div>
                </div> <!-- End Media Section -->
                <!-- Start Media Section -->
                <div class="col-sm-12 clearfix media-section">
                    <div class="media">
                        <div class="media-left">
                            <span class="media-rounded">
                                <img src="<?php echo asset_url('img/megaphone.png') ?>" style="margin-right: 10px;">
                            </span>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">How are the packages on <? echo Config::get('app.name'); ?> Different?</h4>
                            <p class="catamaran">The different packages are designed to accomodate a specified investment amount. The higher the package, the more money you invest. Hence, the more profit you make.</p>
                        </div>
                    </div>
                </div> <!-- End Media Section -->
                <!-- Start Media Section -->
                <div class="col-sm-12 clearfix media-section">
                    <div class="media">
                        <div class="media-left">
                            <span class="media-rounded">
                               <img src="<?php echo asset_url('img/megaphone.png') ?>" style="margin-right: 10px;">
                            </span>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">Who can join <? echo Config::get('app.name'); ?>?</h4>
                            <p class="catamaran">Anybody of any age and sex can join <? echo Config::get('app.name'); ?>. Equal benefits and donations are assigned to all.</p>
                        </div>
                    </div>
                </div> <!-- End Media Section -->
                <!-- Start Media Section -->
                <div class="col-sm-12 clearfix media-section">
                    <div class="media">
                        <div class="media-left">
                            <span class="media-rounded">
                              <img src="<?php echo asset_url('img/megaphone.png') ?>" style="margin-right: 10px;">
                            </span>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">Is Runniing Multiple Accounts Allowed?</h4>
                            <p class="catamaran">Yes, you can run multiple accounts, but you must run them with different usernames and email addresses as those are unique in the system</p>
                        </div>
                    </div>
                </div> <!-- End Media Section -->
                <!-- Start Media Section -->
                <div class="col-sm-12 clearfix media-section">
                    <div class="media">
                        <div class="media-left">
                            <span class="media-rounded">
                             <img src="<?php echo asset_url('img/megaphone.png') ?>" style="margin-right: 10px;">
                            </span>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">How Much Does Setting Up An Account Cost?</h4>
                            <p class="catamaran">Setting up an account with <? echo Config::get('app.name'); ?> is absolutely 100% FREE! No charges whatsoever!!</p>
                        </div>
                    </div>
                </div> <!-- End Media Section -->
                <!-- Start Media Section -->
                <div class="col-sm-12 clearfix media-section">
                    <div class="media">
                        <div class="media-left">
                            <span class="media-rounded">
                            <img src="<?php echo asset_url('img/megaphone.png') ?>" style="margin-right: 10px;">
                            </span>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">How Long Will It Take Get Downlines To Pay Me?</h4>
                            <p class="catamaran">The time it takes for you to get downlines to pay you depends on the flow of new users in the system. To shorten this timeframe, this is why we introduced the autocycle feature which forces new and existing members to keep investing in the system while retaining their profits of course. Downlines are assigned to pay usually on or before 15 days!</p>
                        </div>
                    </div>
                </div> <!-- End Media Section -->
                <!-- Start Media Section -->
                <div class="col-sm-12 clearfix media-section">
                    <div class="media">
                        <div class="media-left">
                            <span class="media-rounded">
                              <img src="<?php echo asset_url('img/megaphone.png') ?>" style="margin-right: 10px;">
                            </span>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">How Can I Join <? echo Config::get('app.name'); ?>?</h4>
                            <p class="catamaran">There are two ways to join <? echo Config::get('app.name'); ?>:</p>
                            <ul class="catamaran">
                                <li><p>You get invited by an existing member via his/her referal link. After clicking the referal link, you will be redirected to a registration form page where you fill a simple registration form. This process takes less than two minutes. After successfully filling the form, you will be granted access to your dashboard where you can join a package and start investing.</p></li>
                                <li><p>In the event where you are not refered by anyone, you can head over to <a href="account/auth/register.php">Register to choose plan</a> and fill out the simple registration form there.</p></li>
                            </ul>
                        </div>
                    </div>
                </div> <!-- End Media Section -->
                <!-- Start Media Section -->
                <div class="col-sm-12 clearfix media-section">
                    <div class="media">
                        <div class="media-left">
                            <span class="media-rounded">
                             <img src="<?php echo asset_url('img/megaphone.png') ?>" style="margin-right: 10px;">
                            </span>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">How Much Compensation Do I Receive From Refering People?</h4>
                            <p class="catamaran">You get 10% of the FIRST package your referals sign up for. For instance, if you refer someone to the system and the person starts by joining the Small package of N5,000, your wallet will be credited with 10% of N5,000 which is N500 naira. However, after that package, the next time your referals invest into the system, you will not receive any compensation bonus.</p>
                        </div>
                    </div>
                </div> <!-- End Media Section -->
                <!-- Start Media Section -->
                <div class="col-sm-12 clearfix media-section">
                    <div class="media">
                        <div class="media-left">
                            <span class="media-rounded">
                           <img src="<?php echo asset_url('img/megaphone.png') ?>" style="margin-right: 10px;">
                            </span>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">How Many Packages Can I Sign Up to at the same Time?</h4>
                            <p class="catamaran">You can only run 2 packages at the same time.</p>
                        </div>
                    </div>
                </div> <!-- End Media Section -->
                <!-- Start Media Section -->
                <div class="col-sm-12 clearfix media-section">
                    <div class="media">
                        <div class="media-left">
                            <span class="media-rounded">
                            <img src="<?php echo asset_url('img/megaphone.png') ?>" style="margin-right: 10px;">
                            </span>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">How Do I Receive Payment?</h4>
                            <p class="catamaran">The downlines the system assigns to pay you will make payments via the bank account details you provide while filling the registration form. They can proceed to make payment either via bank deposit or cash transfer.</p>
                        </div>
                    </div>
                </div> <!-- End Media Section -->
                <!-- Start Media Section -->
                <div class="col-sm-12 clearfix media-section">
                    <div class="media">
                        <div class="media-left">
                            <span class="media-rounded">
                           <img src="<?php echo asset_url('img/megaphone.png') ?>" style="margin-right: 10px;">
                            </span>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">Is <? echo Config::get('app.name'); ?> Legal?</h4>
                            <p class="catamaran"><? echo Config::get('app.name'); ?> is a multi-level interpersonal organization where individuals who will help each other deliberately, will join with their details. Also, the registered members from <? echo Config::get('app.name'); ?> have a bound together monetary relationship, and this has demonstrated the motivation behind why <? echo Config::get('app.name'); ?> is not a subject of legitimate relations thus the <? echo Config::get('app.name'); ?> community can't be illicit. Giving cash by one member to another is not disallowed by either universal or nearby lawful frameworks.</p>
                        </div>
                    </div>
                </div> <!-- End Media Section -->
                <!-- Start Media Section -->
                <div class="col-sm-12 clearfix media-section">
                    <div class="media">
                        <div class="media-left">
                            <span class="media-rounded">
                           <img src="<?php echo asset_url('img/megaphone.png') ?>" style="margin-right: 10px;">
                            </span>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">I Have Made Payment But My Payment Has Not Been Approved?</h4>
                            <p class="catamaran"><? echo Config::get('app.name'); ?> is a timed system. Members are given 24 hours to confirm payments made by their downlines (provided valid evidence of payment has been uploaded). Once this time frame elapses and there is still no approval, the case will be automatically moved to the <? echo Config::get('app.name'); ?> Court where judges will preside over the issue and reach a verdict to confirm or cancel the payment based on the statements and evidences provided by you and the member you were matched to.</p>
                        </div>
                    </div>
                </div> <!-- End Media Section -->
                <!-- Start Media Section -->
                <div class="col-sm-12 clearfix media-section">
                    <div class="media">
                        <div class="media-left">
                            <span class="media-rounded">
                           <img src="<?php echo asset_url('img/megaphone.png') ?>" style="margin-right: 10px;">
                            </span>
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">Help!!! The Downline Assigned to Pay Me Refused to Pay</h4>
                            <p class="catamaran">Keep Calm, there is no need to worry here. Once the time assigned to a downline has been exhausted, the system will block the account of the downline and assign a new downline to pay you within 24 hours.</p>
                        </div>
                    </div>
                </div> <!-- End Media Section -->
            </div>
        </div>
    </section> <!-- End FAQ Section -->

    </section>
    <section id="newsletter" class="newsletter bg-gray">
      <div class="container text-center">
        <h2 style="color:  #fff;">Subscribe to Newsletter</h2>
        <p class="lead">There are many variation passages of lorem ipsum, but the majority have</p>
        <div class="form-holder">
          <form id="newsletterForm" action="" method="POST">
            <div class="form-group">
              <input type="text" name="email" placeholder="Enter Your Email Address" required>
              <button type="submit" name="submit" class="btn btn-primary btn-gradient submit">Subscribe</button>
            </div>
          </form>
        </div>
      </div>
    </section>
   
<?php echo View::make('footer')->render() ?> 