<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> スタッフ管理
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
                    <form role="form" method="post" id="editStaff" role="form">
                        <input type="hidden" name="Password" value=""/>
                        <div class="box-header">
                            <h3 class="box-title">スタッフ編集</h3>
                            <input type="submit" class="btn btn-primary pull-right" value="保存"/>
                        </div><!-- /.box-header -->
                        <input type="hidden" value="save" name='mode' id='mode'>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_name">事業所名</label>
                                        <select class="form-control required" id="company_name" name="company_name">
                                            <option value="0">事業所を選択</option>
                                            <?php
                                            if(!empty($companys))
                                            {
                                                foreach ($companys as $company)
                                                {
                                                    ?>
                                                    <option value="<?php echo $company['company_id'] ?>" <?php if($company['company_id'] == $staff['company_id']) {echo "selected=selected";} ?>><?php echo $company['company_name'] ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="staff_name">スタッフ名：</label>
                                        <input type="text" class="form-control" id="staff_name" placeholder=""
                                               name="staff_name" value="<?php echo $staff['staff_name']; ?>"
                                               maxlength="128">
                                        <input type="hidden" value="<?php echo $staff['staff_id']; ?>"
                                               name="staff_id" id="staff_id"/>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="staff_mail_address">メールアドレス：</label>
                                        <input type="email" class="form-control" id="staff_mail_address" placeholder=""
                                               name="staff_mail_address" value="<?php echo $staff['staff_mail_address']; ?>" maxlength="128" autocomplete="off" value="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="staff_password">パスワード：</label>
                                        <input type="password" class="form-control" id="staff_password" placeholder=""
                                               name="staff_password" maxlength="20" autocomplete="off" value="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="staff_password_confirm">パスワード（確認）：</label>
                                        <input type="password" class="form-control" id="staff_password_confirm"
                                               placeholder="" name="staff_password_confirm" maxlength="20" autocomplete="off" value="">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="role">役職</label>
                                        
                                        <select class="form-control required" id="role" name="role">
                                            <option value="0">役職を選択</option>
                                            <?php
                                            if(!empty($roles))
                                            {
                                                foreach ($roles as $role)
                                                {
                                                    ?>
                                                    <option value="<?php echo $role['roleId'] ?>" <?php if($role['roleId'] == $staff['staff_role']) {echo "selected=selected";} ?>><?php echo $role['role'] ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="jobtype">職種</label>
                                        
                                        <select class="form-control required" id="jobtype" name="jobtype">
                                            <option value="0">職種を選択</option>
                                            <?php
                                            if(!empty($jobtypes))
                                            {
                                                foreach ($jobtypes as $jobtype)
                                                {
                                                    ?>
                                                    <option value="<?php echo $jobtype['jobtypeId'] ?>" <?php if($jobtype['jobtypeId'] == $staff['staff_jobtype']) {echo "selected=selected";} ?>><?php echo $jobtype['jobtype'] ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="employtype">勤務形態</label>
                                        
                                        <select class="form-control required" id="employtype" name="employtype">
                                            <option value="0">勤務形態を選択</option>
                                            <?php
                                            if(!empty($employtypes))
                                            {
                                                foreach ($employtypes as $employtype)
                                                {
                                                    ?>
                                                    <option value="<?php echo $employtype['employtypeId'] ?>" <?php if($employtype['employtypeId'] == $staff['staff_employtype']) {echo "selected=selected";} ?>><?php echo $employtype['employtype'] ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.box-body -->

                        <div class="box-footer">
                            <input type="submit" class="btn btn-primary" value="保存"/>
                            <a class="btn btn-default" href="<?php echo company_url() . 'staff/' ?>">戻る</a>
                            <!--                            <input type="reset" class="btn btn-default" value="クリアー" />-->
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="<?php echo base_url(); ?>assets/js/common.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/js/moment-with-locales.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>

<script type="text/javascript">
  $(function () {
    $('.timepicker').datetimepicker({
      stepping: 1,
      format: 'HH:mm',
    });
  });

  var timer = 0;

  function onFaqSync(id) {
    const base_url = "<?php echo base_url(); ?>";
    if (confirm("メイン→サブにすべての記事を同期してもよろしいですか？")) {
      $("#loading").show();
      $.ajax({
        url: base_url + 'company/company/faq_sync/' + id,
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