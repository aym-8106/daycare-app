<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>管理画面【企業用】</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta http-equiv="Content-Security-Policy" content="script-src 'self' 'unsafe-inline' chrome-extension: https://cdnjs.cloudflare.com https://code.jquery.com; img-src 'self' data: https: chrome-extension:;">
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
        <a href="#"><b>CareNavi通所</b></a>
    </div><!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg" style="font-size: 18px;font-weight: 700;">ログイン</p>
        <div class="row">
            <div class="col-md-12">
                <?php echo validation_errors('<div class="alert alert-danger alert-dismissable">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
            </div>
        </div>
        <?php
        $error = $this->session->flashdata('error');
        if($error)
        {
            ?>
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?php echo $error; ?>
            </div>
        <?php }
        $success = $this->session->flashdata('success');
        if($success)
        {
            ?>
            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?php echo $success; ?>
            </div>
        <?php } ?>
        <form method="post" action="<?php echo base_url('login'); ?>">
            <!-- <div class="form-group has-feedback">
                <select class="form-control required" id="company_id" name="company_id" onchange="javascript:getStaff();">
                    <option value="0">事業所を選択</option>
                    <?php
                    // if(!empty($companys))
                    // {
                    //     foreach ($companys as $company)
                    //     {
                    //         ?>
                    //         <option value="<?php //echo $company['company_id'] ?>" <?php //if($company['company_id'] == set_value('company_id')) {echo "selected=selected";} ?>><?php //echo $company['company_name'] ?></option>
                    //         <?php
                    //     }
                    // }
                    ?>
                </select>
            </div>
            <div class="form-group has-feedback">
                <div id="loading" style="display: none;">同期しています。
                    <img alt="同期" src="<?php //echo base_url() ?>assets/images/loading.gif">
                </div>
                <select class="form-control required" id="staff_id" name="staff_id">
                    <option value="0">スタッフを選択</option>
                    <?php
                    // if(!empty($staffs))
                    // {
                    //     foreach ($staffs as $staff)
                    //     {
                    //         ?>
                    //         <option value="<?php //echo $staff['staff_id'] ?>" <?php //if($staff['staff_id'] == set_value('staff_id')) {echo "selected=selected";} ?>><?php //echo $staff['staff_name'] ?></option>
                    //         <?php
                    //     }
                    // }
                    ?>
                </select>
            </div> -->
            <div class="form-group has-feedback">
                <input type="email" class="form-control" placeholder="メールアドレス" autocomplete="off" name="email" required />
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" placeholder="パスワード" autocomplete="off" name="password" required />
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-12 text-right margin-bottom">
                    <a href="<?php echo base_url() ?>forgotPassword">パスワードをお忘れの方 <i class="fa fa-arrow-circle-right"></i></a><br>
                </div>
            </div>

            <div class="row margin-bottom">
                <div class="col-xs-12">
                    <input type="submit" class="btn btn-primary btn-lg btn-block btn-flat" style="border-radius: 5px;" value="ログイン" />
                </div><!-- /.col -->
            </div>
            <div class="row margin">
                <div class="col-xs-8">
                    <div class="checkbox icheck">
                        <label>
                            <input name="remember" type="checkbox" value="remember"> 次回から自動的にログイン
                        </label>
                    </div>
                </div>
            </div>
        </form>


    </div><!-- /.login-box-body -->
</div><!-- /.login-box -->

<script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script>
    // Chrome拡張機能関連のエラーを抑制
    window.addEventListener('error', function(e) {
        // Chrome拡張機能のエラーを無視
        if (e.filename && e.filename.indexOf('chrome-extension://') === 0) {
            e.preventDefault();
            return false;
        }
    });

    // リソース読み込みエラーを抑制
    window.addEventListener('unhandledrejection', function(e) {
        if (e.reason && e.reason.message && e.reason.message.indexOf('chrome-extension://') !== -1) {
            e.preventDefault();
            return false;
        }
    });
</script>
<script>
    function getStaff() {
        var id = $("#company_id").val();
        const base_url = "<?php echo base_url(); ?>";
        $("#loading").show();
        $.ajax({
            url: base_url + 'login/getstaff/' + id,
            type: 'post',
            dataType: 'json',
        }).done(function (res) {
            $("#loading").hide();
            console.log("elapse: ",res.time)
            console.log(res);
            if (res.ok == true) {
                var opt = '<option value="0">スタッフを選択</option>';
                for (let i = 0; i < res.cnt; i++) {
                    opt += '<option value="'+ res.staffs[i].staff_id +'">' + res.staffs[i].staff_name + '</option>';
                }
                $('#staff_id').html(opt);
            } else {
                alert('エラーが発生しました。');
            }
        }).fail(function () {
            $("#loading").hide();
            alert("メイン→サブに記事のコピー中にエラーが発生しました。");
        });
    }
</script>
</body>
</html>