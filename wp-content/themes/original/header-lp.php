<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/lp.css">
<?php wp_head(); ?>
</head>
<body id="<?php echo isset($post) ? esc_attr($post->post_name) : ''; ?>" <?php body_class(); ?>>
<header class="ly_header <?php echo esc_attr($header_class); ?>">
  <div class="ly_header_inner">
    <div class="ly_header_hd">
      <div class="ly_header_logo"><a href="/lp/"><img src="<?php echo get_template_directory_uri(); ?>/img/lp/logo.png" alt="訪問看護"></a></div>
      <div class="ly_header_menu js_menu_toggle">
        <span></span>
        <span></span>
        <em class="js_menuTxt">MENU</em>
      </div>
    </div>
    <nav class="ly_header_nav js_nav">
      <ul class="list">
        <li><a href="#anc01">事業内容</a></li>
        <li><a href="#anc02">事業計画</a></li>
        <li><a href="#anc03">導入実績</a></li>
        <li><a href="#anc04">お問い合わせ</a></li>
      </ul>
    </nav>
  </div>
</header>