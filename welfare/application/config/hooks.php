<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

/**
 * セキュリティヘッダーの設定
 */
$hook['post_controller_constructor'] = array(
    'class'    => 'Security_headers',
    'function' => 'set_security_headers',
    'filename' => 'Security_headers.php',
    'filepath' => 'hooks'
);
