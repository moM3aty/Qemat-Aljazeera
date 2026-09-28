<?php
/* ============================================================
 *  config.php   —   المسار:  /config.php
 *  الإعدادات العامة + الاتصال بقاعدة البيانات + دوال مساعدة
 * ============================================================ */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* ===================== بيانات قاعدة البيانات ===================== */
define('DB_HOST', 'localhost');
define('DB_NAME', 'u439595347_qimah_site');   // اسم قاعدة البيانات
define('DB_USER', 'u439595347_qimahAdmin');         // مستخدم قاعدة البيانات
define('DB_PASS', 'c002X68X|A');             // كلمة المرور

/* ===================== مجلد رفع الصور ===================== */
define('UPLOAD_PATH', __DIR__ . '/uploads');
define('UPLOAD_URL',  'uploads');

/* ===================== الاتصال ===================== */
if (!defined('SKIP_DB_CONNECT')) {
    try {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    } catch (PDOException $e) {
        die('<div style="font-family:Tahoma;padding:30px;text-align:center">
             تعذر الاتصال بقاعدة البيانات.<br>تأكد من بيانات الاتصال في ملف <b>config.php</b>
             </div>');
    }
}

/* ===================== الإعدادات ===================== */
function settings_all()
{
    global $pdo;
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach ($pdo->query("SELECT `key`, `value` FROM settings") as $row) {
            $cache[$row['key']] = $row['value'];
        }
    }
    return $cache;
}

function setting($key, $default = '')
{
    $all = settings_all();
    return (isset($all[$key]) && $all[$key] !== '') ? $all[$key] : $default;
}

/* ===================== أدوات مساعدة ===================== */
function e($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function is_logged_in()
{
    return !empty($_SESSION['admin_id']);
}

function require_login()
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function csrf_token()
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field()
{
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

function csrf_check()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'])) {
            die('طلب غير صالح (CSRF).');
        }
    }
}

function make_slug($text)
{
    $text = trim((string)$text);
    $text = preg_replace('/\s+/u', '-', $text);
    $text = preg_replace('/[^\p{L}\p{N}\-]+/u', '', $text);
    $text = trim($text, '-');
    return $text !== '' ? mb_strtolower($text, 'UTF-8') : 'post';
}

function unique_slug($pdo, $slug, $ignoreId = 0)
{
    $base = $slug;
    $i = 2;
    while (true) {
        $st = $pdo->prepare("SELECT id FROM posts WHERE slug = ? AND id <> ? LIMIT 1");
        $st->execute([$slug, $ignoreId]);
        if (!$st->fetch()) {
            return $slug;
        }
        $slug = $base . '-' . $i;
        $i++;
    }
}

/**
 * رفع صورة وحفظها داخل مجلد uploads
 * @return string|null  المسار النسبي للصورة أو null عند الفشل
 */
function upload_image($file, $prefix = 'img')
{
    if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    if ($file['size'] > 6 * 1024 * 1024) { // 6 ميجا
        return null;
    }

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/pjpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];

    $mime = @mime_content_type($file['tmp_name']);
    if (!$mime || !isset($allowed[$mime])) {
        return null;
    }

    if (!is_dir(UPLOAD_PATH)) {
        @mkdir(UPLOAD_PATH, 0755, true);
    }

    $name = $prefix . '_' . date('YmdHis') . '_' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_PATH . '/' . $name)) {
        return null;
    }
    return UPLOAD_URL . '/' . $name;
}

function delete_uploaded($path)
{
    $path = (string)$path;
    if (strpos($path, UPLOAD_URL . '/') === 0) {
        $full = __DIR__ . '/' . $path;
        if (is_file($full)) {
            @unlink($full);
        }
    }
}

function flash($msg, $type = 'success')
{
    $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
}

function get_flash()
{
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}