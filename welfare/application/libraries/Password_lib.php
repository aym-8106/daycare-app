<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * パスワード管理ライブラリ
 * bcryptを使用した安全なパスワードハッシュ化
 */
class Password_lib
{
    protected $CI;
    protected $cost = 12; // bcryptのコスト（デフォルト12）

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    /**
     * パスワードをハッシュ化
     *
     * @param string $password 平文パスワード
     * @return string ハッシュ化されたパスワード
     */
    public function hash($password)
    {
        if (empty($password)) {
            return false;
        }

        return password_hash($password, PASSWORD_BCRYPT, ['cost' => $this->cost]);
    }

    /**
     * パスワードを検証
     *
     * @param string $password 平文パスワード
     * @param string $hash ハッシュ化されたパスワード
     * @return bool 検証結果
     */
    public function verify($password, $hash)
    {
        if (empty($password) || empty($hash)) {
            return false;
        }

        // 新しいbcryptハッシュの場合
        if (substr($hash, 0, 4) === '$2y$' || substr($hash, 0, 4) === '$2a$' || substr($hash, 0, 4) === '$2b$') {
            return password_verify($password, $hash);
        }

        // 古いsha1ハッシュとの互換性（移行期間中のみ）
        if (strlen($hash) === 40) {
            return sha1($password) === $hash;
        }

        return false;
    }

    /**
     * パスワードが再ハッシュが必要かチェック
     *
     * @param string $hash ハッシュ化されたパスワード
     * @return bool 再ハッシュが必要かどうか
     */
    public function needs_rehash($hash)
    {
        if (empty($hash)) {
            return true;
        }

        // 古いsha1ハッシュの場合は再ハッシュが必要
        if (strlen($hash) === 40) {
            return true;
        }

        return password_needs_rehash($hash, PASSWORD_BCRYPT, ['cost' => $this->cost]);
    }

    /**
     * ランダムなパスワードを生成
     *
     * @param int $length パスワードの長さ
     * @return string 生成されたパスワード
     */
    public function generate($length = 12)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';
        $max = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, $max)];
        }

        return $password;
    }

    /**
     * パスワード強度をチェック
     *
     * @param string $password チェックするパスワード
     * @return array チェック結果
     */
    public function check_strength($password)
    {
        $score = 0;
        $feedback = [];

        // 長さチェック
        if (strlen($password) >= 8) {
            $score += 25;
        } else {
            $feedback[] = '8文字以上にしてください';
        }

        // 小文字チェック
        if (preg_match('/[a-z]/', $password)) {
            $score += 15;
        } else {
            $feedback[] = '小文字を含めてください';
        }

        // 大文字チェック
        if (preg_match('/[A-Z]/', $password)) {
            $score += 15;
        } else {
            $feedback[] = '大文字を含めてください';
        }

        // 数字チェック
        if (preg_match('/[0-9]/', $password)) {
            $score += 15;
        } else {
            $feedback[] = '数字を含めてください';
        }

        // 特殊文字チェック
        if (preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $score += 30;
        } else {
            $feedback[] = '特殊文字を含めてください';
        }

        // 強度レベル
        if ($score >= 90) {
            $level = 'very_strong';
            $level_text = '非常に強い';
        } elseif ($score >= 70) {
            $level = 'strong';
            $level_text = '強い';
        } elseif ($score >= 50) {
            $level = 'medium';
            $level_text = '普通';
        } elseif ($score >= 30) {
            $level = 'weak';
            $level_text = '弱い';
        } else {
            $level = 'very_weak';
            $level_text = '非常に弱い';
        }

        return [
            'score' => $score,
            'level' => $level,
            'level_text' => $level_text,
            'feedback' => $feedback,
            'is_strong' => $score >= 70
        ];
    }
}