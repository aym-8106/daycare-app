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

            <div class="col-md-12">

                <div class="box">
                <?php
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
                <form action="<?php echo admin_url() ?>profile" method="post" id="editProfile" role="form">
                    <input type="hidden" name="mode" value="save">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_name">氏名</label>
                                    <input type="text" class="form-control" id="user_name" name="user_name" value="<?php
                                        $default_name = '';
                                        if (isset($user['name'])) {
                                            $default_name = $user['name'];
                                        } elseif (isset($user['staff_name'])) {
                                            $default_name = $user['staff_name'];
                                        } elseif (isset($user['admin_name'])) {
                                            $default_name = $user['admin_name'];
                                        }
                                        echo set_value('user_name', $default_name);
                                    ?>" maxlength="128" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_email">メールアドレス</label>
                                    <input type="text" class="form-control" id="user_email" name="user_email" value="<?php
                                        $default_email = '';
                                        if (isset($user['email'])) {
                                            $default_email = $user['email'];
                                        } elseif (isset($user['staff_mail_address'])) {
                                            $default_email = $user['staff_mail_address'];
                                        } elseif (isset($user['admin_email'])) {
                                            $default_email = $user['admin_email'];
                                        }
                                        echo set_value('user_email', $default_email);
                                    ?>">
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="old_password">現在のパスワード：</label>
                                    <input type="password" class="form-control" id="old_password" placeholder="パスワード" name="old_password" maxlength="20" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="new_password">新しいパスワード：</label>
                                    <input type="password" class="form-control" id="new_password" placeholder="" name="new_password" maxlength="20" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="new_password_confirm">新しいパスワード（確認）：</label>
                                    <input type="password" class="form-control" id="new_password_confirm" placeholder="" name="new_password_confirm" maxlength="20" required>
                                </div>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                    <div class="box-footer">
                        <input type="submit" class="btn btn-primary" value="保存" />
                    </div>
                </form>
                </div>

            </div>
        </div>    
    </section>
</div>

<script src="<?php echo base_url(); ?>assets/js/editUser.js" type="text/javascript"></script>