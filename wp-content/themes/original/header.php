<!DOCTYPE html>
<html lang="ja">
<head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="robots" content="noindex" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="" />
        <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/style.css">
        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script src="<?php echo get_template_directory_uri(); ?>/js/script.js"></script>
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
<?php wp_head(); ?>
    </head>
    <body>
	<header class="js-header">
		<div class="header_inner">
        <div class="header_logo header_logo_sp">
          <a href="<?php echo home_url( '/' ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt=""></a>
        </div>
			<div class="header_nav">
				<ul>
					<li><a href="#sec01">事業内容</a></li>
					<li><a href="#sec02">事業計画</a></li>
					<li><a href="#sec03">導入実績</a></li>
					<li><a href="#sec04">お問い合わせ</a></li>
				</ul>
			</div>
       </div>
       <div class="sp_menu">
        <div class="toggle">
          <span class="toggle-span"></span>
          <span></span>
          <span></span>
        </div>
          <nav class="nav-menu">
				  <div class="g-menu">
					<ul>
    <li><a href="#sec01">事業内容</a></li>
    <li><a href="#sec02">事業計画</a></li>
    <li><a href="#sec03">導入実績</a></li>
    <li><a href="#sec04">お問い合わせ</a></li>
</ul>
			  </div>
          </nav>

       </div>
     </header>