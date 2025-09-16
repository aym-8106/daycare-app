<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>管理画面【企業用】</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link href="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="#"><b>事業所新規登録</b></a>
        </div><!-- /.login-logo -->
            <div class="row">
                <div class="col-md-12">
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
                <div class="col-md-12">
                <!-- general form elements -->

                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">事業所追加</h3>
                        </div><!-- /.box-header -->
                        <!-- form start -->

                        <form role="form" id="addUser" action="<?php echo company_url() ?>regist" method="post" role="form">
                            <input type="hidden" name="mode" value="save">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-12">                                
                                        <div class="form-group">
                                            <label for="company_name">事業所名：</label>
                                            <input type="text" class="form-control required" value="<?php echo set_value('company_name'); ?>" id="company_name" name="company_name" maxlength="128">
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="company_email">メールアドレス：</label>
                                            <input type="text" class="form-control required email" id="company_email" value="<?php echo set_value('company_email'); ?>" name="company_email" maxlength="128">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="company_password">パスワード：</label>
                                            <input type="password" class="form-control required" id="company_password" name="company_password" maxlength="20">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="cpassword">パスワード（確認）：</label>
                                            <input type="password" class="form-control required equalTo" id="company_password_confirm" name="company_password_confirm" maxlength="20">
                                        </div>
                                    </div>
                                </div>
                            </div><!-- /.box-body -->
        
                            <div class="box-footer">
                                <input type="submit" class="btn btn-primary" value="保存" />
                                <a class="btn btn-default" href="<?php echo company_url().'login/'?>">戻る</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>    
        </div>
  </body>
</html>