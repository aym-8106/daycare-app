<?php
$staff_id = $staff['staff_id'];
$staff_name = $staff['staff_name'];
$staff_mail_address = $staff['staff_mail_address'];
$staff_password = $staff['staff_password'];
?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <i class="fa fa-user-circle"></i> プロフィール
        <small>情報変更 </small>
      </h1>
    </section>
    
    <section class="content">
    
        <div class="row">
            <!-- left column -->
            <div class="col-md-3">
              <!-- general form elements -->
                <div class="box box-warning">
                    <div class="box-body box-profile">
                        <img class="profile-user-img img-responsive img-circle" src="<?php echo base_url(); ?>assets/dist/img/avatar.png" alt="User profile picture">
                        <h3 class="profile-username text-center"><?= $staff_name ?></h3>

                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <b>メール</b> <a class="pull-right"><?= $staff_mail_address ?></a>
                            </li>
<!--                            <li class="list-group-item">-->
<!--                                <b>電話番号</b> <a class="pull-right">--><?//= $mobile ?><!--</a>-->
<!--                            </li>-->
                        </ul>
                    </div>
                </div>

            </div>

            <div class="col-md-5">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="<?= ($active == "details")? "active" : "" ?>"><a href="#details" data-toggle="tab">プロフィール詳細</a></li>
                        <li class="<?= ($active == "changepass")? "active" : "" ?>"><a href="#changepass" data-toggle="tab">パスワード変更</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="<?= ($active == "details")? "active" : "" ?> tab-pane" id="details">
                            <form action="<?php echo base_url() ?>profile/profileUpdate" method="post" id="editProfile" role="form">
                                <?php $this->load->helper('form'); ?>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-12">                                
                                            <div class="form-group">
                                                <label for="staff_name">名前</label>
                                                <input type="text" class="form-control" id="staff_name" name="staff_name" placeholder="<?php echo $staff_name; ?>" value="<?php echo set_value('staff_name', $staff_name); ?>" maxlength="128" />
                                                <input type="hidden" value="<?php echo $staff_id; ?>" name="userId" id="userId" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="staff_mail_address">メールアドレス</label>
                                                <input type="text" class="form-control" id="staff_mail_address" name="staff_mail_address" placeholder="<?php echo $staff_mail_address; ?>" value="<?php echo set_value('staff_mail_address', $staff_mail_address); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- /.box-body -->
                                <div class="box-footer">
                                    <input type="submit" class="btn btn-primary" value="保存" />
                                    <input type="reset" class="btn btn-default" value="クリアー" />
                                </div>
                            </form>
                        </div>
                        <div class="<?= ($active == "changepass")? "active" : "" ?> tab-pane" id="changepass">
                            <form role="form" action="<?php echo base_url() ?>profile/changePassword" method="post">
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputPassword1">現在のパスワード：</label>
                                                <input type="password" class="form-control" id="inputOldPassword" placeholder="パスワード" name="oldPassword" maxlength="20" required>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputPassword1">新しい パスワード：</label>
                                                <input type="password" class="form-control" id="inputPassword1" placeholder="" name="newPassword" maxlength="20" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="inputPassword2">新しいパスワード（確認）：</label>
                                                <input type="password" class="form-control" id="inputPassword2" placeholder="" name="cNewPassword" maxlength="20" required>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- /.box-body -->
            
                                <div class="box-footer">
                                    <input type="submit" class="btn btn-primary" value="保存" />
                                    <input type="reset" class="btn btn-default" value="クリアー" />
                                </div>
                            </form>
                        </div>                        
                    </div>
                </div>
            </div>
            <div class="col-md-4">
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

                <?php  
                    $noMatch = $this->session->flashdata('nomatch');
                    if($noMatch)
                    {
                ?>
                <div class="alert alert-warning alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?php echo $this->session->flashdata('nomatch'); ?>
                </div>
                <?php } ?>
                
                <div class="row">
                    <div class="col-md-12">
                        <?php echo validation_errors('<div class="alert alert-danger alert-dismissable">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
                    </div>
                </div>
            </div>
        </div>    
    </section>
</div>

<script src="<?php echo base_url(); ?>assets/js/editUser.js" type="text/javascript"></script>