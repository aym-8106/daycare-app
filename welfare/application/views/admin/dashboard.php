<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-tachometer" aria-hidden="true"></i>ダッシュボード
        </h1>
    </section>

    <section class="content">
        <!-- 管理者用機能メニュー -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-user-circle"></i> 管理者機能</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-4">
                                <a href="<?php echo admin_url(); ?>attendance" class="btn btn-success btn-block btn-lg">
                                    <i class="fa fa-clock-o"></i><br>出退勤管理
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="<?php echo base_url(); ?>schedule" class="btn btn-info btn-block btn-lg">
                                    <i class="fa fa-calendar"></i><br>スケジュール
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="<?php echo base_url(); ?>post" class="btn btn-warning btn-block btn-lg">
                                    <i class="fa fa-comments"></i><br>申し送り
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 管理統計情報 -->
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

        <!-- 管理者専用メニュー -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-cog"></i> 管理者専用機能</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3">
                                <a href="<?php echo admin_url(); ?>attendance" class="btn btn-primary btn-block">
                                    <i class="fa fa-list"></i><br>勤怠管理
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="<?php echo admin_url(); ?>staff" class="btn btn-primary btn-block">
                                    <i class="fa fa-users"></i><br>スタッフ管理
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="<?php echo admin_url(); ?>company" class="btn btn-primary btn-block">
                                    <i class="fa fa-building"></i><br>事業所管理
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="<?php echo admin_url(); ?>history" class="btn btn-primary btn-block">
                                    <i class="fa fa-history"></i><br>履歴確認
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>