      <div class="copyrights text-center">
        <p>© 2018 <?php echo Config::get('app.name'); ?> All Right Reserved
          <!-- Please do not remove the backlink to us unless you support further theme's development at https://bootstrapious.com/donate. It is part of the license conditions. Thank you for understanding :)-->
        </p>
      </div>
    </div>
    <!-- JavaScript files-->
    <script src="<?php echo asset_url('May/vendor/jquery/jquery.min.js') ?>"></script>
    <script src="<?php echo asset_url('May/vendor/popper.js/umd/popper.min.js') ?>"> </script>
    <script src="<?php echo asset_url('May/vendor/bootstrap/js/bootstrap.min.js') ?>"></script>
    <script src="<?php echo asset_url('May/vendor/jquery.cookie/jquery.cookie.js') ?>"> </script>
    <script src="<?php echo asset_url('May/vendor/chart.js/Chart.min.js') ?>"></script>
    <script src="<?php echo asset_url('May/vendor/jquery-validation/jquery.validate.min.js') ?>"></script>
    <!-- Main File-->
    <script src="<?php echo asset_url('May/js/front.js') ?>"></script>
    <script>
var video = document.getElementById("myVideo");
var btn = document.getElementById("myBtn");

function myFunction() {
  if (video.paused) {
    video.play();
    btn.innerHTML = "Pause";
  } else {
    video.pause();
    btn.innerHTML = "Play";
  }
}
</script>
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
