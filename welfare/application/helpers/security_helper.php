<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * セキュリティ関連のヘルパー関数
 */

if (!function_exists('csrf_token')) {
    /**
     * CSRFトークンのhidden inputを生成
     */
    function csrf_token()
    {
        $CI =& get_instance();
        return '<input type="hidden" name="' . $CI->security->get_csrf_token_name() . '" value="' . $CI->security->get_csrf_hash() . '">';
    }
}

if (!function_exists('csrf_meta_tag')) {
    /**
     * CSRFトークンのメタタグを生成（AJAX用）
     */
    function csrf_meta_tag()
    {
        $CI =& get_instance();
        return '<meta name="csrf-token" content="' . $CI->security->get_csrf_hash() . '">';
    }
}

if (!function_exists('escape_html')) {
    /**
     * HTML エスケープ（XSS対策）
     */
    function escape_html($str)
    {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('sanitize_input')) {
    /**
     * 入力データのサニタイズ
     */
    function sanitize_input($data)
    {
        if (is_array($data)) {
            return array_map('sanitize_input', $data);
        }

        // 危険な文字をエスケープ
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');

        return $data;
    }
}

if (!function_exists('validate_email_secure')) {
    /**
     * 安全なメールアドレス検証
     */
    function validate_email_secure($email)
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (!function_exists('generate_secure_password')) {
    /**
     * 安全なパスワードの生成
     */
    function generate_secure_password($length = 12)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        $max = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, $max)];
        }

        return $password;
    }
}