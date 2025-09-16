<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> 事業所管理
            <small>作成, 編集, 削除</small>
        </h1>
    </section>

    <section class="content">

        <div class="row">
            <!-- left column -->
            <div class="" style="padding: 0 20px;">
                <?php
                $this->load->helper('form');
                $error = $this->session->flashdata('error');
                if ($error) {
                    ?>
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $this->session->flashdata('error'); ?>
                    </div>
                <?php } ?>
                <?php
                $success = $this->session->flashdata('success');
                if ($success) {
                    ?>
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $this->session->flashdata('success'); ?>
                    </div>
                <?php } ?>

                <div class="row">
                    <div class="col-md-12">
                        <?php echo validation_errors('<div class="alert alert-danger alert-dismissable">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="box box-primary">
                    <!-- form start -->
                    <form role="form" method="post" id="editUser" role="form">
                        <input type="hidden" name="Password" value=""/>
                        <div class="box-header">
                            <h3 class="box-title">事業所編集</h3>
                            <input type="submit" class="btn btn-primary pull-right" value="保存"/>
                        </div><!-- /.box-header -->
                        <input type="hidden" value="save" name='mode' id='mode'>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_name">事業所名：</label>
                                        <input type="text" class="form-control" id="company_name" placeholder=""
                                               name="company_name" value="<?php echo $company['company_name']; ?>"
                                               maxlength="128">
                                        <input type="hidden" value="<?php echo $company['company_id']; ?>"
                                               name="company_id" id="company_id"/>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_email">メールアドレス：</label>
                                        <input type="email" class="form-control" id="company_email" placeholder=""
                                               name="company_email" value="<?php echo $company['company_email']; ?>"
                                               maxlength="128">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_password">パスワード：</label>
                                        <input type="password" class="form-control" id="company_password" placeholder=""
                                               name="company_password" maxlength="20" autocomplete="off" value="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_password_confirm">パスワード（確認）：</label>
                                        <input type="password" class="form-control" id="company_password_confirm"
                                               placeholder="" name="company_password_confirm" maxlength="20" autocomplete="off" value="">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="payment_date">有効期間：</label>
                                        
                                        <input id="payment_date" type="text" name="payment_date" value="<?php echo $company['payment_date']; ?>"
                                            class="form-control datepicker" placeholder="" autocomplete="off"/>
                                    </div>
                                </div>
                            </div>

                        </div><!-- /.box-body -->

                        <div class="box-footer">
                            <input type="submit" class="btn btn-primary" value="保存"/>
                            <a class="btn btn-default" href="<?php echo admin_url() . 'company/' ?>">戻る</a>
                            <!--                            <input type="reset" class="btn btn-default" value="クリアー" />-->
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="<?php echo base_url(); ?>assets/js/common.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.ja.min.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('.datepicker').datepicker({
            language: "ja",
            autoclose: true,
            format: "yyyy-mm-dd"
        });
    });

  var timer = 0;

  function onFaqSync(id) {
    const base_url = "<?php echo base_url(); ?>";
    if (confirm("メイン→サブにすべての記事を同期してもよろしいですか？")) {
      $("#loading").show();
      $.ajax({
        url: base_url + 'admin/company/faq_sync/' + id,
        type: 'post',
        dataType: 'json',
      }).done(function (res) {
        $("#loading").hide();
        console.log("elapse: ",res.time)
        if (res.ok == true) {
          alert(res.cnt + '件の記事を同期しました。');
        } else {
          alert('エラーが発生しました。');
        }
      }).fail(function () {
        $("#loading").hide();
        alert("メイン→サブに記事のコピー中にエラーが発生しました。");
      });

    }
  }
</script>