          <!-- Page Footer-->
          <footer class="main-footer">
            <div class="container-fluid">
              <div class="row">
                <div class="col-sm-6">
                  <p>&copy; <?php echo date('Y', time()) . ' ' . Config::get('app.name'); ?> All Right Reserved  |   API Version <?php echo Config::get('app.version'); ?></p>
                </div>
                <div class="col-sm-6 text-right">
                 <p>Design by <a href="https://olakunlevpn.com" class="external">Olakunlevpn</a></p>
                </div>
              </div>
            </div>
          </footer>
        </div>
      </div>
    </div>
    <!-- JavaScript files-->



        <!-- Rest of the code <--></-->

    <script src="<?php echo asset_url('May/vendor/popper.js/umd/popper.min.js') ?>"> </script>
    <script src="<?php echo asset_url('May/vendor/bootstrap/js/bootstrap.min.js') ?>"></script>
<script src="<?php echo asset_url('May/js/front.js') ?>"></script>

     <div id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" class="modal fade text-left">
                        <div role="document" class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h4 id="exampleModalLabel" class="modal-title">Signin Modal</h4>
                              <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
                            </div>
                            <div class="modal-body">
                              <p>Lorem ipsum dolor sit amet consectetur.</p>
                              <form>
                                <div class="form-group">
                                  <label>Email</label>
                                  <input type="email" placeholder="Email Address" class="form-control">
                                </div>
                                <div class="form-group">
                                  <label>Password</label>
                                  <input type="password" placeholder="Password" class="form-control">
                                </div>
                                <div class="form-group">
                                  <input type="submit" value="Signin" class="btn btn-primary">
                                </div>
                              </form>
                            </div>
                            <div class="modal-footer">
                              <button type="button" data-dismiss="modal" class="btn btn-secondary">Close</button>
                              <button type="button" class="btn btn-primary">Save changes</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

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

