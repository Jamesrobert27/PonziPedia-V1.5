<?php require_once 'app/init.php'; 
// +------------------------------------------------------------------------+
// | @author Olakunlevpn (Olakunlevpn)
// | @author_url 1: http://www.maylancer.cf
// | @author_url 2: https://codecanyon.net/user/gr0wthminds
// | @author_email: olakunlevpn@live.com   
// +------------------------------------------------------------------------+
// | PonziPedia - Peer 2 Peer 50% ROI Donation System
// | Copyright (c) 2018 PonziPedia. All rights reserved.
// +------------------------------------------------------------------------+

?>

<?php echo View::make('header')->render() ?>

  <section>
      <div class="container">
        <div class="row block">
          <div class="col-lg-9">
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item">API </li>
            </ul>
            <h1>API <?php echo Config::get('app.version'); ?></h1>
            <div class="setting-well">
                    <p>Our API allows you to retrieve informations from our website via GET request and supports the following query parameters: </p>
                    <table class="table table-striped table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Meaning</th>
                                <th>Values</th>
                                <th>Description</th>
                                <th>Required</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b>type</b></td>
                                <td>Query type.</td>
                                <td>get_user_data, posts_data</td>
                                <td>This parameter specify the type of the query.</td>
                                <td><i class="fa fa-check"></i></td>
                            </tr>
                            <tr>
                                <td><b>limit</b></td>
                                <td>Limit of items.</td>
                                <td>LIMIT</td>
                                <td>This parameter specify the limit of items. Max:100 | Default:20</td>
                                <td><i class="fa fa-remove"></i></td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                    <div style="font-size:18px;">How to start?</div>
                    <hr>
                    <ol style="list-style: initial;">
                      
                        <li>Once you have created the app, you'll get APP_ID, and APP_SECRET. <br>Example: <br><br> <img src="https://s3.postimg.org/4vaacpclf/Screenshot_24.png" alt=""><br><br></li>
                        <li>To start the Oauth process, use the link <?php echo Config::get('app.url'); ?>oauth?app_id={YOUR_APP_ID}<br><br></li>
                        <li>Once the end user clicks this link, he/she will be redirected to the authorization page.<br><br></li>
                        <li>Once the end user authorization the app, he/she will be redirected to your domain name with a GET parameter "code", example: http://yourdomain/?code=XXX<br><br></li>
                        <li>
                            In your code, to retrieve the authorized user info, you need to generate an access code, please use the code below:<br><br>
                            <ol style="list-style: initial;">
                                <li>
                                    PHP:
                                    <code>
                                        
                                        <pre>&lt;?php
$app_id = 'YOUR_APP_ID'; // your application app id
$app_secret = 'YOUR_APP_SECRET'; your application app secret
$code = $_GET['code']; // the GET parameter you got in the callback: http://yourdomain/?code=XXX

$get = file_get_contents("<?php echo Config::get('app.url'); ?>authorize?app_id={$app_id}&amp;app_secret={$app_secret}&amp;code={$code}");

$json = json_decode($get, true);
if (!empty($json['access_token'])) {
    $access_token = $json['access_token']; // your access token
}
?&gt;</pre>
                                                                            </code>
                                </li>
                            </ol>
                        </li>
                        <li>
                            Once you got the access code, simple call the data you would like to retrieve, Example: <br><br>
                            <ol style="list-style: initial;">
                                <li>PHP:
                                    <code>
                                    <pre>if (!empty($json['access_token'])) {
   $access_token = $json['access_token']; // your access token
   $type = "get_user_data"; // or posts_data
   $get = file_get_contents("<?php echo Config::get('app.url'); ?>app_api?access_token={$access_token}&amp;type={$type}");
}
</pre>                                    </code>
                                </li>
                                <li>
                                    Respond:
                                    <pre><b>Json</b>output
{
    "api_status": "success",
    "api_version": "1.3",
    "user_data": {
        "id": "",
        "username": "",
        "first_name": "",
        "last_name": "",
        "gender": "",
        "birthday": "",
        "about": "",
        "website": "",
        "facebook": "",
        "twitter": "",
        "vk": "",
        "google+": "",
        "profile_picture": "",
        "cover_picture": "",
        "verified": "",
        "url": ""
    }
}
</pre>
                                </li>
                            </ol>
                        </li>
                    </ol>
                </div>
          </div>
        </div>
      </div>
    </section>
<?php echo View::make('footer')->render() ?> 