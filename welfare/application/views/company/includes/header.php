<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $title; ?></title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link rel="icon" type="image/png" href="/favicon.png">
    <!-- Bootstrap 3.3.4 -->
    <link href="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"
          type="text/css"/>
    <!-- FontAwesome 4.3.0 -->
    <link href="<?php echo base_url(); ?>assets/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet"
          type="text/css"/>
    <!-- Ionicons 2.0.0 -->
    <link href="<?php echo base_url(); ?>assets/bower_components/Ionicons/css/ionicons.min.css" rel="stylesheet"
          type="text/css"/>
    <!-- Theme style -->
    <link href="<?php echo base_url(); ?>assets/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css"/>
    <!-- AdminLTE Skins. Choose a skin from the css/skins 
         folder instead of downloading all of them to reduce the load. -->
    <link href="<?php echo base_url(); ?>assets/dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootoast@1.0.1/dist/bootoast.min.css">
    <link href="<?php echo base_url(); ?>assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo base_url(); ?>assets/css/common.css" rel="stylesheet" type="text/css"/>
    <style>
        .error {
            color: red;
            font-weight: normal;
        }
    </style>
    <script src="<?php echo base_url(); ?>assets/js/jquery-2.2.2.min.js"></script>
    <script src="https://code.jquery.com/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/plugins/select2/js/select2.min.js"></script>
    <script type="text/javascript">
        var baseURL = "<?php echo base_url(); ?>";
    </script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/font-awesome/css/font-awesome.min.css">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <header class="main-header">
        <!-- Logo -->
        <a href="<?php echo base_url(); ?>company/payment" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>事業所管理</b></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>事業所管理画面</b></span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">ナビメニューをトグル</span>
            </a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="<?php echo base_url(); ?>assets/dist/img/avatar.png" class="user-image"
                                 alt="User Image"/>
                            <span class="hidden-xs"><?php echo($user['company_name']); ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">

                                <img src="<?php echo base_url(); ?>assets/dist/img/avatar.png" class="img-circle"
                                     alt="User Image"/>
                                <p>
                                    <?php echo $user['company_name']; ?>
                                    <small><?php echo '管理者'; ?></small>
                                </p>

                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="<?php echo company_url(); ?>profile" class="btn btn-default btn-flat"><i
                                                class="fa fa-user-circle"></i> プロフィール</a>
                                </div>
                                <div class="pull-right">
                                    <a href="<?php echo company_url(); ?>logout" class="btn btn-default btn-flat"><i
                                                class="fa fa-sign-out"></i> ログアウト</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu">
                <li class="header"></li>
                <?php if ($role == ROLE_COMPANY) { ?>
                    <?php if ($page == 'payment'){ ?><li class="active"><?php }else{ ?><li><?php }?>
                        <a href="<?php echo company_url(); ?>payment">
                            <i class="fa fa-building"></i>
                            <span>決済管理</span>
                        </a>
                    </li>

                    <?php if ($page == 'staff'){ ?><li class="active"><?php }else{ ?><li><?php }?>
                        <a href="<?php echo company_url(); ?>staff">
                            <i class="fa fa-users"></i>
                            <span>スタッフ管理</span>
                        </a>
                    </li>
                    <li <?php if ($page == 'schedule'){ ?> class="active" <?php } ?> >
                        <a href="<?php echo company_url(); ?>schedule">
                            <i class="fa fa-file-text-o"></i> <span>スケジュール管理</span>
                        </a>
                    </li>
                    <li <?php if ($page == 'shift'){ ?> class="active" <?php } ?> >
                        <a href="<?php echo company_url(); ?>shift">
                            <i class="fa fa-calendar"></i> <span>シフト管理</span>
                        </a>
                    </li>
                    <?php if ($page == 'profile'){ ?><li class="active"><?php }else{ ?><li><?php }?>
                    <a href="<?php echo company_url(); ?>profile">
                        <i class="fa fa-cog"></i>
                        <span>情報変更</span>
                    </a>
                    </li>

                <?php } ?>
            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>