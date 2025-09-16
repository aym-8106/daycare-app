<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-tachometer" aria-hidden="true"></i>ダッシュボード
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-lg-6 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <p>事業所数</p>
                        <h3><?php echo $company_count; ?><span style="font-size: 2rem;">社.</span></h3>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="<?php echo admin_url(); ?>company" class="small-box-footer">もっと見る<i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div><!-- ./col -->
            <div class="col-lg-6 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-green">
                    <div class="inner">
                        <p>スタッフ数</p>
                        <h3><?php echo $staff_count; ?><span style="font-size: 2rem;">人.</span></h3>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="<?php echo admin_url(); ?>staff" class="small-box-footer">もっと見る<i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div><!-- ./col -->
        </div>
    </section>
</div>