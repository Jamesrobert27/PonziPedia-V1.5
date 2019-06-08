<?php include '_header.php';




 ?>

<div class="content-inner">
          <!-- Page Header-->
          <header class="page-header">
            <div class="container-fluid">
              <h2 class="no-margin-bottom">Edit Profile</h2>
            </div>
          </header>
       <?php if (isset($_POST['submit'])){
            $firstname = $_POST['firstname'];
            $lastname  = $_POST['lastname'];
            $phonenumber = $_POST['phonenumber'];
            $bankname = $_POST['bankname'];
            $accountnumber = $_POST['accountnumber'];
            $accountname = $_POST['accountname'];
            $accounttype = $_POST['accounttype'];
            $country = $_POST['country'];
            $state = $_POST['state'];
             if ($accounttype =="NULL") {
                echo '<div class="alert alert-danger" role="alert">
                Your cant submit empty account type
              </div>';
            }
            ProfileComplete($firstname,$lastname,$phonenumber,$bankname,$accountnumber,$accountname,$accounttype,$country,$state,$user_id);
           }
         ?>
          <?php  

       if (isset($_GET['edit'])) {
          $user = DB::table('userdetails')->where('userid', $user_id)->first();
          if ($user->userid == $_GET['edit']) {

            if (isset($_POST['edit-submit'])){
            $firstname = $_POST['firstname'];
            $lastname  = $_POST['lastname'];
            $phonenumber = $_POST['phonenumber'];
            $bankname = $_POST['bankname'];
            $accountnumber = $_POST['accountnumber'];
            $accountname = $_POST['accountname'];
            $accounttype = $_POST['accounttype'];
            $country = $_POST['country'];
            $state = $_POST['state'];
             if ($accounttype =="NULL") {
                echo '<div class="alert alert-danger" role="alert">
                Your cant submit empty account type
              </div>';
            }
            ProfileUpdate($firstname,$lastname,$phonenumber,$bankname,$accountnumber,$accountname,$accounttype,$country,$state,$user_id);
           }
            ?>
     <section class="dashboard-counts no-padding-bottom">
        
        <div class="row">
      
       <div class="col-md-4">
      <div class="list-group">
                    <a href="account.php?edit=<?php echo $user_id; ?>" class="list-group-item list-group-item-success">Edit Details</a>
                    <a href="settings.php?p=profile" class="list-group-item">Profile</a>
                    <a href="settings.php?p=account" class="list-group-item">Account</a>
                    <a href="settings.php?p=password" class="list-group-item">Password </a>
                    <a href="settings.php??p=connect" class="list-group-item"><?php echo _e('main.connect') ?></a>
                </div>
        </div>
       
           <div class="col-md-8">
                  <div class="card">
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4">Edit Details</h3>
                    </div>
                    <div class="card-body">
                      <form method="POST" action="">
                        <div class="form-group">
                          <label class="form-control-label">First Name</label>
                          <input type="text" name="firstname" placeholder="First name" value="<?php echo $user->firstname; ?>" class="form-control">
                        </div>
                        <div class="form-group">       
                         <label class="form-control-label">Last Name</label>
                          <input type="text" name="lastname" value="<?php echo $user->lastname; ?>" placeholder="Last Name" class="form-control">
                        </div>

                          <div class="form-group">       
                         <label class="form-control-label">Phone Numner (e.g. 08051234567,2348051234567)</label>
                          <input type="text" name="phonenumber" value="<?php echo $user->phonenumber; ?>" placeholder="Phone Numner" class="form-control">
                        </div>

                          <div class="form-group">       
                         <label class="form-control-label">Bank Name</label>
                          <input type="text" name="bankname" value="<?php echo $user->bankname; ?>" placeholder="Bank Name" class="form-control">
                        </div>

                          <div class="form-group">       
                         <label class="form-control-label">Account Number</label>
                          <input type="text" name="accountnumber" value="<?php echo $user->accountnumber; ?>" placeholder="Account Number" class="form-control">
                        </div>

                          <div class="form-group">       
                         <label class="form-control-label">Account Name</label>
                          <input type="text" name="accountname" value="<?php echo $user->accountname; ?>" placeholder="Account Name" class="form-control">
                        </div>

                          <div class="form-group">       
                         <label class="form-control-label">Account Type</label>
                          <select id="accounttype" name="accounttype" required="required" class="form-control">
                            <option value="NULL" selected="selected">Select Account Type</option>
                            <option value="Savings">Savings</option><option value="Current">Current</option>
                          </select>
                        </div>

                          <div class="form-group">       
                         <label class="form-control-label">Country</label>
                          <input type="text" name="country" value="<?php echo $user->country; ?>" placeholder="Your Country" class="form-control">
                        </div>
                          <div class="form-group">       
                         <label class="form-control-label">State</label>
                          <input type="text" name="state" value="<?php echo $user->state; ?>" placeholder="Your State" class="form-control">
                        </div>


                        <div class="form-group">       
                          <input type="submit" name="edit-submit" value="Update & continue" class="btn btn-primary">
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
        </div>

    </section>
            <?php
          }
          else{
            echo '<div class="alert alert-danger" role="alert">
                The link you follow has expired, please try again
         </div>';
          }
       }
       else
       {
        $CheckUuser = DB::table('userdetails')->where('userid', $user_id)->count();
if ($CheckUuser >=1) {
  redirect_to(App::url('account/settings.php?p=profile'));
}
        ?>
                <div class="col-lg-10">
                  <div class="card">
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4">Account Details</h3>
                    </div>
                    <div class="card-body">
                      <p style="color: green;">Please complete your profile to continue.</p>
                      <form method="POST" action="">
                        <div class="form-group">
                          <label class="form-control-label">First Name</label>
                          <input type="text" name="firstname" placeholder="First name" class="form-control" required>
                        </div>
                        <div class="form-group">       
                         <label class="form-control-label">Last Name</label>
                          <input type="text" name="lastname" placeholder="Last Name" class="form-control" required>
                        </div>

                          <div class="form-group">       
                         <label class="form-control-label">Phone Numner (e.g. 08051234567,2348051234567)</label>
                          <input type="text" name="phonenumber" placeholder="Phone Numner" class="form-control" required>
                        </div>

                          <div class="form-group">       
                         <label class="form-control-label">Bank Name</label>
                          <input type="text" name="bankname" placeholder="Bank Name" class="form-control" required>
                        </div>

                          <div class="form-group">       
                         <label class="form-control-label">Account Number</label>
                          <input type="text" name="accountnumber" placeholder="Account Number" class="form-control" required>
                        </div>

                          <div class="form-group">       
                         <label class="form-control-label">Account Name</label>
                          <input type="text" name="accountname" placeholder="Account Name" class="form-control" required>
                        </div>

                          <div class="form-group">       
                         <label class="form-control-label">Account Type</label>
                          <select id="accounttype" name="accounttype" required="required" class="form-control">
                            <option value="NULL" selected="selected">Select Account Type</option>
                            <option value="Savings">Savings</option><option value="Current">Current</option>
                          </select>
                        </div>

                          <div class="form-group">       
                         <label class="form-control-label">Country</label>
                          <input type="text" name="country" placeholder="Your Country" class="form-control" required>
                        </div>
                          <div class="form-group">       
                         <label class="form-control-label">State</label>
                          <input type="text" name="state" placeholder="Your State" class="form-control" required>
                        </div>


                        <div class="form-group">       
                          <input type="submit" name="submit" value="Update & continue" class="btn btn-primary">
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
       
         
     </section>
<?php
}
?>

<?php include '_footer.php'; ?>
