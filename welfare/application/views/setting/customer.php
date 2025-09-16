<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>利用者設定ページ</h1>
    </section>
    
    <section class="content">
        <div class="box-body">
            <div class="col-lg-4 col-xs-1"></div>
            <div class="col-lg-4 col-xs-10">
                <form action="<?php echo base_url() ?>setting/customer" method="POST" id="form1" name="form1" class="form-horizontal">
                    
                    <div class="form-group">
                        <div class="setting-row">
                                <label class="bg-light-blue control-label">名前: </label>
                                <label class="bg-light-blue control-label"><?php echo($user['staff_name']); ?>さん</label>
                        </div>
                    </div>

                    <?php 
                    $days = array('月', '火', '水', '木','金','土', '日');
                    foreach($days as $day) { ?>
                        <div class="form-group">
                            <div class="setting-row">
                                <label class="bg-light-blue control-label"><?php echo($day); ?></label>

                                <label class="control-label bg-light-blue"><?php echo('看護orリハビリ'); ?></label>
                                <label class="control-label bg-light-blue"><?php echo('所要時間'); ?></label>
                                <label class="control-label bg-light-blue"><?php echo('頻度'); ?></label>
                            </div>
                        </div>
                    <?php } ?>
                </form>
            </div><!-- ./col -->
            <div class="col-lg-4 col-xs-1"></div>
        </div>
    </section>
</div>