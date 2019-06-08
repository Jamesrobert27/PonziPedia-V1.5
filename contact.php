<?php 
// +------------------------------------------------------------------------+
// | @author Olakunlevpn (Olakunlevpn)
// | @author_url 1: http://www.maylancer.cf
// | @author_url 2: https://codecanyon.net/user/gr0wthminds
// | @author_email: olakunlevpn@live.com   
// +------------------------------------------------------------------------+
// | PonziPedia - Peer 2 Peer 50% ROI Donation System
// | Copyright (c) 2018 PonziPedia. All rights reserved.
// +------------------------------------------------------------------------+
require_once 'app/init.php';
 ?>

<?php echo View::make('header')->render() ?>

  <section>
      <div class="container">
        <div class="row block">
          <div class="col-lg-9">
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item">Contact Us</li>
            </ul>
            <h1>Contact <?php echo Config::get('app.name'); ?></h1>
            <p class="lead"><?php echo Config::get('app.name'); ?> support is here to help. Learn more about popular topics and find resources that will help you with all of your <?php echo Config::get('app.name'); ?> services.</p>
          </div>
        </div>

        <?php 
        if (isset($_POST['sendMail'])) {

          $email = $_POST['email'];
          $departmant = $_POST['support'];
          $subject = $_POST['subject'];
          $message = $_POST['message'];
         if ($departmant == "NULL") {
           echo   '<div id="top-alert" class="alert alert-danger" role="alert" style="
    padding-top: 50px !important;
    padding-bottom: 50px;font-size: 30px;
">
           <button type="button" class="close"><a href="contact.php" class="btn btn-danger btn-sm">Try again</a></button>
       <strong>OOPS!</strong> You need to select the suport department, please Try again!
     </div>';
         }
         else{
          $to = Config::get('app.webmail');
         $Contactmessage = 'I need help from your '.$subject.' department below is my request<br><br>'.$message;
          $header ="From: <".Config::get('app.name').">\r\n" .
          "Reply-To: ".Config::get('app.name')."\r\n"; 
         $header .= "MIME-Version: 1.0\r\n";
          $header .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
   // if(mail($to,$subject,$Contactmessage,$header)){
    
  
      echo  '<div id="top-alert" class="alert alert-success" role="alert" style="
    padding-top: 50px !important;
    padding-bottom: 50px;font-size: 30px;
">
      <button type="button" class="close" data-dismiss="alert">×</button>
      <strong>Message Sent!</strong>Your support message has been submitted successfully. Respond time is approximately 1 hour or less.
      </div>';
  //}
   //else {
    //     echo   '<div id="top-alert" class="alert alert-danger" role="alert">
   //  <button type="button" class="close" data-dismiss="alert">×</button>
   //  <strong>Sending Failed</strong> Error while sending your message to the support system, please Try again Later!
  // </div>';
  //}
 } 
}
        else{?>
        <div class="col-md-9">
         <form method="POSt" action="">
           <div class="form-group">
            <label>Email Address</label>
           <input type="email" name="email" class="form-control" placeholder="your email address" required>
         </div>
          <div class="form-group">
            <label>Department </label>
           <select class="c-select" class="form-control" name="support">
             <option value="NULL" selected>Please choose support departmant</option>
             <option value="customercare">Customar care</option>
             <option value="marginteam">Margin Team</option>
             <option value="Technical">Technical Department</option>
             <option value="apirequest">API Request</option>
             <option value="bugreport">Bug Report</option>
           </select>
         </div>
          <div class="form-group">
            <label>Subject</label>
           <input type="text" name="subject" class="form-control" class="Your subject here" required>
         </div>

          <div class="form-group">
            <label>Message</label>
           <textarea type="text" name="message" class="form-control" class="Your message here" style="margin-top: 0px; margin-bottom: 0px; height: 170px;"></textarea>
         </div>

          <div class="form-group">
           <input type="submit" name="sendMail" class="btn btn-primary btn-lg" value="Send Message" >
         </div>
         </form>
        </div>
       <?php }?>
        </div>
      </div>
    </section>
<?php echo View::make('footer')->render() ?> 