 <div id="scrollTop">
      <div class="d-flex align-items-center justify-content-end"><i class="fa fa-long-arrow-up"></i>To Top</div>
    </div>
    <footer class="main-footer">
      <div class="container">
        <div class="row">
          <div class="col-lg-3 col-md-6"><a href="#" class="brand"><?php echo Config::get('app.name'); ?></a>
            <ul class="contact-info list-unstyled">
              <li><a href="mailto:<?php echo Config::get('app.webmail'); ?>"><?php echo Config::get('app.webmail'); ?></a></li>
              <li><a href="tel:<?php echo Config::get('app.phone'); ?>"><?php echo Config::get('app.phone'); ?></a></li>
            </ul>
            <ul class="social-icons list-inline">
              <li class="list-inline-item"><a href="#" target="_blank" title="Facebook"><i class="fa fa-facebook"></i></a></li>
              <li class="list-inline-item"><a href="#" target="_blank" title="Twitter"><i class="fa fa-twitter"></i></a></li>
              <li class="list-inline-item"><a href="#" target="_blank" title="Instagram"><i class="fa fa-instagram"></i></a></li>
              <li class="list-inline-item"><a href="#" target="_blank" title="Pinterest"><i class="fa fa-pinterest"></i></a></li>
            </ul>
          </div>
          <div class="col-lg-3 col-md-6">
            <h5>Protected Page</h5>
            <ul class="links list-unstyled">
              <li> <a href="account/discussion.php">Discussion</a></li>
              <li> <a href="account/support.php">Support</a></li>
              <li> <a href="account/message.php">Messages</a></li>
              <li> <a href="account/tickets.php">Tickets</a></li>
            </ul>
          </div>
          <div class="col-lg-3 col-md-6">
            <h5>About <?php echo Config::get('app.name'); ?></h5>
            <ul class="links list-unstyled">
              <li> <a href="about.php">About Us</a></li>
              <li> <a href="api.php">API 1.0</a></li>
              <li> <a href="contact.php">Developer</a></li>
              <li> <a href="contact.php">Contact Us</a></li>
            </ul>
          </div>
          <div class="col-lg-3 col-md-6">
            <h5>Social Account</h5>
            <ul class="links list-unstyled">
              <li> <a href="https://www.facebook.com/<?php echo Config::get('app.facebook'); ?>" target="_blank">Facebook</a></li>
              <li> <a href="https://api.whatsapp.com/send?phone=<?php echo Config::get('app.phone'); ?>&Hey%20i%20need%20your%20Support%20for%20<?php echo Config::get('app.name'); ?>" target="_blank">Whatsapp</a></li>
              <li> <a href="<?php echo Config::get('app.telegram'); ?>" target="_blank">Telegram</a></li>
              <li> <a href="https://www.youtube.com/user/<?php echo Config::get('app.youtube'); ?>" target="_blank">Youtube</a></li>
            </ul>
          </div>
        </div>
      </div>
      <div class="copyrights">
        <div class="container">
          <div class="row">
            <div class="col-md-7">
              <p>&copy; <?php echo date('Y', time()) . ' ' . Config::get('app.name'); ?>. All rights reserved.                        </p>
            </div>
            <div class="col-md-5 text-right">
              <p>Developed By <a href="https://olakunlevpn.com" target="_BLANK" class="external">Olakunlevpn</a>  </p>
              <!-- Please do not remove the backlink to Bootstrapious unless you support us at http://bootstrapious.com/donate. It is part of the license conditions. Thanks for understanding :) -->
            </div>
          </div>
        </div>
      </div>
    </footer>
    <!-- Javascript files-->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"> </script>
    <script src="<?php echo asset_url('frontpage/vendor/bootstrap/js/bootstrap.min.js') ?>"></script>
    <script src="<?php echo asset_url('frontpage/vendor/jquery.cookie/jquery.cookie.js') ?>"> </script>
    <script src="<?php echo asset_url('frontpage/vendor/owl.carousel/owl.carousel.min.js') ?>"></script>
    <script src="<?php echo asset_url('frontpage/jjs/front.js') ?>"></script>
    <!-- Google Analytics: change UA-XXXXX-X to be your site's ID.-->
    <!---->
   <script>
     function video_lead_play_state(element, active)
{
    var $active = $(element).closest(".js-video-lead").find(".btn-play-active");
    var $default = $(element).closest(".js-video-lead").find(".btn-play-default");

    if (active) {
        $active.show();
        $default.hide();
    } else {
        $active.hide();
        $default.show();
    }
}


$(document).ready(function () {
    // hide the videos and show the images
    var $videos = $(".js-video-lead iframe");
    $videos.hide();
    $(".js-video-lead > img").not(".btn-play").show();

    // position the video holders
    $(".js-video-lead").css("position", "relative");

    // prevent autoplay on load and add the play button
    $videos.each(function (index, video) {
        var $video = $(video);

        // prevent autoplay due to normal navigation
        var url = $video.attr("src");
        if (url.indexOf("&autoplay") > -1) {
            url = url.replace("&autoplay=1", "");
        } else {
            url = url.replace("?autoplay=1", "");
        }
        $video.attr("src", url).removeClass(
            "js-video-lead-autoplay"
        );

        // add and position the play button
        var top = parseInt(parseFloat($video.css("height")) / 2) - 15;
        var left = parseInt(parseFloat($video.css("width")) / 2) - 21;
        var $btn_default = $("<img />").attr("src", "<?php echo asset_url('frontpage/img/index.png') ?>").css({
            "position": "absolute",
            "top": top + "px",
            "left": left + "px",
             "z-index": 110,
             "width": 70
        }).addClass("btn-play btn-play-default");
        var $btn_active = $("<img />").attr("src", "<?php echo asset_url('frontpage/img/index.png') ?>").css({
            "display": "none",
            "position": "absolute",
            "top": top + "px",
            "left": left + "px",
            "z-index": 110,
             "width": 70
        }).addClass("btn-play btn-play-active");
        $(".js-video-lead").append($btn_default).append($btn_active);
    });


    $(".js-video-lead img").on("click", function (event) {
        var $holder = $(this).closest(".js-video-lead");
        var $video = $holder.find("iframe");
        var url = $video.attr("src");
        url += (url.indexOf("?") > -1) ? "&" : "?";
        url += "autoplay=1";
        $video.addClass("js-video-lead-autoplay").attr("src", url);
        $holder.find("img").remove();
        $video.show();
    });

    $(".js-video-lead > img").on("mouseenter", function (event) {
        video_lead_play_state(this, true);
    });

    $(".js-video-lead > img").not(".btn-play").on("mouseleave", function (event) {
        video_lead_play_state(this, false);
    });
});
   </script>
   <?php echo View::make('modals.load')->render() ?>
   <!--Start of Zendesk Chat Script-->
<script type="text/javascript">
window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute("charset","utf-8");
$.src="https://v2.zopim.com/?5gfA3DkvU6B8iN8hbeK15dwn8FCf3yOa";z.t=+new Date;$.
type="text/javascript";e.parentNode.insertBefore($,e)})(document,"script");
</script>
<!--End of Zendesk Chat Script-->
  </body>
</html>