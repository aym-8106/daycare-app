<?php


// タイトルタグの出力

add_theme_support( 'title-tag' );

add_theme_support('post-thumbnails');

function wpcf7_custom_email_validation_filter( $result, $tag ) {
  if ( 'your-email-confirm' == $tag->name ) {
    $your_email = isset( $_POST['your-email'] ) ? trim( $_POST['your-email'] ) : '';
    $your_email_confirm = isset( $_POST['your-email-confirm'] ) ? trim( $_POST['your-email-confirm'] ) : '';
    if ( $your_email != $your_email_confirm ) {
      $result->invalidate( $tag, "メールアドレスが一致しません" );
    }
  }
  return $result;
}
add_filter( 'wpcf7_validate_email', 'wpcf7_custom_email_validation_filter', 20, 2 );
add_filter( 'wpcf7_validate_email*', 'wpcf7_custom_email_validation_filter', 20, 2 );

function column_category_list_shortcode($atts) {
    $atts = shortcode_atts([
        'taxonomy' => 'column_cat', // タクソノミースラッグ
    ], $atts);

    $taxonomy = $atts['taxonomy'];
    $categories = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);

    if (is_wp_error($categories) || empty($categories)) {
        return '<p>カテゴリーが見つかりません。</p>';
    }

    // 現在のカテゴリーを取得
    $current_term = get_queried_object();
    $current_term_id = $current_term instanceof WP_Term ? $current_term->term_id : null;

    $output = '<ul>';
    foreach ($categories as $category) {
        // 現在のカテゴリーと一致する場合にクラスを追加
        $class = ($category->term_id === $current_term_id) ? 'current-category' : '';
        $output .= sprintf(
            '<li class="%s"><a href="%s">%s</a></li>',
            esc_attr($class),
            esc_url(get_term_link($category)),
            esc_html($category->name)
        );
    }
    $output .= '</ul>';

    return $output;
}
add_shortcode('column_category_list', 'column_category_list_shortcode');


function faq_category_list_shortcode($atts) {
    $atts = shortcode_atts([
        'taxonomy' => 'faq_cat', // タクソノミースラッグ
    ], $atts);

    $taxonomy = $atts['taxonomy'];
    $categories = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);

    if (is_wp_error($categories) || empty($categories)) {
        return '<p>カテゴリーが見つかりません。</p>';
    }

    // 現在のカテゴリーを取得
    $current_term = get_queried_object();
    $current_term_id = $current_term instanceof WP_Term ? $current_term->term_id : null;

    $output = '<ul>';
    foreach ($categories as $category) {
        // 現在のカテゴリーと一致する場合にクラスを追加
        $class = ($category->term_id === $current_term_id) ? 'current-category' : '';
        $output .= sprintf(
            '<li class="%s"><a href="%s">%s</a></li>',
            esc_attr($class),
            esc_url(get_term_link($category)),
            esc_html($category->name)
        );
    }
    $output .= '</ul>';

    return $output;
}
add_shortcode('faq_category_list', 'faq_category_list_shortcode');