<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * セキュリティヘッダーを設定するフック
 */
class Security_headers
{
    public function set_security_headers()
    {
        // XSS Protection
        header('X-XSS-Protection: 1; mode=block');

        // Content Type Options
        header('X-Content-Type-Options: nosniff');

        // Frame Options (Clickjacking Protection)
        header('X-Frame-Options: SAMEORIGIN');

        // Referrer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');

        // Content Security Policy (基本設定 + Chrome拡張機能対応)
        $csp_policy = "default-src 'self'; " .
                     "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://code.jquery.com chrome-extension:; " .
                     "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; " .
                     "font-src 'self' https://fonts.gstatic.com; " .
                     "img-src 'self' data: https: chrome-extension:; " .
                     "connect-src 'self' chrome-extension:;";

        header("Content-Security-Policy: $csp_policy");

        // Strict Transport Security (HTTPS使用時のみ)
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }
}