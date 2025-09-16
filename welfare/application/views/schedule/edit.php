<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>出退勤ページ</h1>
    </section>
    
    <section class="content">
        <div class="box-body">
            <div class="col-lg-4 col-xs-2"></div>
            <div class="col-lg-4 col-xs-8 text-center">
                <label class="">
                    <?php echo($user['staff_name']); ?>さん
                </label>
            </div>
            <div class="col-lg-4 col-xs-2"></div>
        </div>
        <div class="box-body">
            <div class="col-lg-4 col-xs-2"></div>
            <div class="col-lg-4 col-xs-8 text-center">
                <!-- small box -->
                <a href="<?php echo base_url(); ?>attendance/list" class="btn btn-primary btn-block btn-flat">出勤</a>
            </div><!-- ./col -->
            <div class="col-lg-4 col-xs-2"></div>
        </div>
        <div class="box-body">
            <div class="col-lg-4 col-xs-2"></div>
            <div class="col-lg-4 col-xs-8 text-center">
                <!-- small box -->
                <a href="<?php echo base_url(); ?>attendance/list" class="btn btn-primary btn-block btn-flat">退勤</a>
            </div><!-- ./col -->
            <div class="col-lg-4 col-xs-2"></div>
        </div>
        <div class="box-body">
            <div class="col-lg-4 col-xs-2"></div>
            <div class="col-lg-4 col-xs-8 text-center">
                <!-- small box -->
                <a href="<?php echo base_url(); ?>attendance/overtime" class="btn btn-primary btn-block btn-flat">残業</a>
            </div><!-- ./col -->
            <div class="col-lg-4 col-xs-2"></div>
        </div>
    </section>
</div>