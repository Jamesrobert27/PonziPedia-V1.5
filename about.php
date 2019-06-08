<?php require_once 'app/init.php'; ?>

<?php echo View::make('header')->render() ?>

  <section>
      <div class="container">
        <div class="row block">
          <div class="col-lg-9">
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item">About Us</li>
            </ul>
            <h1>About <?php echo Config::get('app.name'); ?></h1>
            <p class="lead">This organization was created by a group of zealous people seeking to promote human welfare and provide a springboard for individuals and corporate organizations alike to thrive via financial empowerment.</p>
                        <p style="text-align:justify">It is a well known fact that there is a huge gap between the rich and the poor. Our organization aims to significantly reduce that gap with the aim of creating a platform that enables all our members attain a higher standard of living and all round financial freedom. This works by evenly distributing all investments among members to bring everybody up at the same time.</p>
                        <p style="text-align:justify">Join our organization today and say goodbye to poverty and financial struggles.</p>
          </div>
        </div>
        <div class="row d-flex align-items-center block">
          <div class="col-lg-6 image"><img src="<?php echo asset_url('img/back.png') ?>" alt="..." class="img-fluid"></div>
          <div class="col-lg-6 text">
            <p>You are already a winner with <?php echo Config::get('app.name'); ?>, what can be lacking, is the start-up investment. We, at <?php echo Config::get('app.name'); ?>, are convinced that through our on-line and online training system you will be able to achieve your personal, professional and financial goals.</p>
          </div>
        </div>
        <div class="row d-flex align-items-center block">
          <div class="col-lg-6 text text-right order-2 order-lg-1">
            <p><?php echo Config::get('app.name'); ?> has an experienced and distinguished Staff and guiders in the level distribution market, with advanced business generation strategies and state-of-the-art management software, providing its members with security and tranquility in managing their business with 100% assurance on 70% return of investment on their total donation.


Through the success accumulated with the multilevel channel, it was invited to innovate the distribution to one of the most coveted ROI system in the world. <?php echo Config::get('app.name'); ?> is born out of the need to constitute an exclusive channel, totally dedicated to its area of ​​activity and market participation.</p>
          </div>
          <div class="col-lg-6 image order-1 order-lg-2"><img src="<?php echo asset_url('frontpage/img/Desktop12.png') ?>" alt="..." class="img-fluid"></div>
        </div>
        <div class="block">       
          <blockquote class="blockquote">
            <p class="mb-0">In investing, what is comfortable is rarely profitable, 
At times, you will have to step out of your comfort zone to realize significant gains. Know the boundaries of your comfort zone and practice stepping out of it in small doses. As much as you need to know the market, you need to know yourself too. Can you handle staying in when everyone else is jumping ship? Or getting out during the biggest rally of the century? There's no room for pride in this kind of self-analysis. The best investment strategy can turn into the worst if you don't have the stomach to see it through.</p>
            <footer class="blockquote-footer">Someone famous in 
              <cite title="Source Title">May</cite>
            </footer>
          </blockquote>
        </div>
        <div class="row block no-margin-bottom">
          <div class="col-lg-9">
            <h3>Benefits of <?php echo Config::get('app.name'); ?> Affiliate System </h3>
            <p><strong>All users can easily promote our website to firsnds, families and colleagues to earn commission for every click and succesful registration. all user can easily track all the information related to the user traffic generated, profit made, and transactions from the frontend. It can also send an email campaign to the users.</p>
            <h2>Guiders Benefits</h2>
            <ol>
              <li>Guiders will get profit of 5% from all members registered under their tree (EG: When user one use your referral link as guider you will receive instant activation fees from the registered member and when user one invite members with their referral link under your family tree you will earn 5% from each users packages purchased.</li>
              <li>Guiders can easly view their family tree from the Guider-panel if you have actived guiders permission.</li>
            </ol>
            <h3>Simple Earning Terms</h3>
            <ul>
              <li>All members earn 1000 NGN activation fees from all registered users with the referral link.</li>
              <li>User cookies are stored for 30 days which means when user visit with your link to and join our system after 29 days you will still earn your referral commission 100% </li>
            </ul>
          </div>
        </div>
      </div>
    </section>
<?php echo View::make('footer')->render() ?> 