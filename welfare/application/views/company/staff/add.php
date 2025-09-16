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
            <div class="col-md-8">
                <?php
                $this->load->helper('form');
                $error = $this->session->flashdata('error');
                if($error)
                {
                    ?>
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $this->session->flashdata('error'); ?>
                    </div>
                <?php } ?>
                <?php
                $success = $this->session->flashdata('success');
                if($success)
                {
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
        </div>

        <div class="row">
            <!-- left column -->
            <div class="col-md-8">
              <!-- general form elements -->

                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">スタッフ追加</h3>
                    </div><!-- /.box-header -->
                    <!-- form start -->

                    <form role="form" id="addStaff" action="<?php echo company_url() ?>staff/add" method="post" role="form">
                        <input type="hidden" name="mode" value="save">
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
                                                    <option value="<?php echo $company['company_id'] ?>" <?php if($company['company_id'] == set_value('company_id')) {echo "selected=selected";} ?>><?php echo $company['company_name'] ?></option>
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
                                        <input type="text" class="form-control required" value="<?php echo set_value('staff_name'); ?>" id="staff_name" name="staff_name" maxlength="128">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="staff_mail_address">メールアドレス：</label>
                                        <input type="email" class="form-control required" id="staff_mail_address" name="staff_mail_address" maxlength="255">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="staff_password">パスワード：</label>
                                        <input type="password" class="form-control required" id="staff_password" name="staff_password" maxlength="20">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cpassword">パスワード（確認）：</label>
                                        <input type="password" class="form-control required equalTo" id="staff_password_confirm" name="staff_password_confirm" maxlength="20">
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
                                                    <option value="<?php echo $role['roleId'] ?>" <?php if($role['roleId'] == set_value('roleId')) {echo "selected=selected";} ?>><?php echo $role['role'] ?></option>
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
                                                    <option value="<?php echo $jobtype['jobtypeId'] ?>" <?php if($jobtype['jobtypeId'] == set_value('jobtypeId')) {echo "selected=selected";} ?>><?php echo $jobtype['jobtype'] ?></option>
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
                                                    <option value="<?php echo $employtype['employtypeId'] ?>" <?php if($employtype['employtypeId'] == set_value('employtypeId')) {echo "selected=selected";} ?>><?php echo $employtype['employtype'] ?></option>
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
                            <input type="submit" class="btn btn-primary" value="保存" />
                            <a class="btn btn-default" href="<?php echo company_url().'staff/'?>">戻る</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>    
    </section>
    
</div>
<script src="<?php echo base_url(); ?>assets/js/addStaff.js" type="text/javascript"></script>
