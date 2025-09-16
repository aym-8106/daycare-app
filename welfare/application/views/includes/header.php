<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo (empty($title)) ?  '管理画面': $title; ?></title>
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

    <!-- Select2 CSS & JS CDN 読み込み（head または footer に） -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- FullCalendar CSS -->
    <!-- <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet' /> -->
    <!-- FullCalendar JS -->
    <!-- <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/locales/ja.global.min.js"></script> -->

    <!-- <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/resource-timegrid@6.1.8/index.global.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fullcalendar/resource-timegrid@6.1.8/main.min.css" rel="stylesheet" /> -->

</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <header class="main-header">
        <!-- Logo -->
        <a href="<?php echo base_url(); ?>attendance" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>管理</b></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg">CareNavi訪問看護</span>
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
                            <span class="hidden-xs"><?php echo($user['staff_name']); ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">

                                <img src="<?php echo base_url(); ?>assets/dist/img/avatar.png" class="img-circle"
                                     alt="User Image"/>
                                <p>
                                    <?php echo($user['staff_name']); ?>
                                </p>

                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="<?php echo base_url(); ?>profile" class="btn btn-default btn-flat"><i
                                                class="fa fa-user-circle"></i> プロフィール</a>
                                </div>
                                <div class="pull-right">
                                    <a href="<?php echo base_url(); ?>logout" class="btn btn-default btn-flat"><i
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
                <li <?php if ($page == 'attendance'){ ?> class="active" <?php } ?> >
                    <a href="<?php echo base_url(); ?>attendance">
                        <i class="fa fa-clock-o"></i> <span>出退勤</span>
                    </a>
                </li>
                <li <?php if ($page == 'schedule'){ ?> class="active" <?php } ?> >
                    <a href="<?php echo base_url(); ?>schedule">
                        <i class="fa fa-calendar-plus-o"></i> <span>スケジュール</span>
                    </a>
                </li>
                <li <?php if ($page == 'post'){ ?> class="active" <?php } ?> >
                    <a href="<?php echo base_url(); ?>post">
                        <i class="fa fa-sticky-note-o"></i> <span>申し送り</span>
                    </a>
                </li>
                <li <?php if ($page == 'patient'){ ?> class="active" <?php } ?> >
                    <a href="<?php echo base_url(); ?>setting/patient">
                        <i class="fa fa-user-o"></i> <span>利用者設定</span>
                    </a>
                </li>
                <li <?php if ($page == 'staff'){ ?> class="active" <?php } ?> >
                    <a href="<?php echo base_url(); ?>setting/staff">
                        <i class="fa fa-user-circle-o"></i> <span>スタッフ設定</span>
                    </a>
                </li>
                <li <?php if ($page == 'instruction'){ ?> class="active" <?php } ?> >
                    <a href="<?php echo base_url(); ?>instruction">
                        <i class="fa fa-file-text-o"></i> <span>指示書管理</span>
                    </a>
                </li>
                <li <?php if ($page == 'report'){ ?> class="active" <?php } ?> >
                    <a href="<?php echo base_url(); ?>report">
                        <i class="fa fa-pencil-square-o"></i> <span>日報管理</span>
                    </a>
                </li>
                <!--li>
                    <hr/>
                </li-->
            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>