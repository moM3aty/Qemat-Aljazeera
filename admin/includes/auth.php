<?php
/* ============================================================
 *  admin/includes/auth.php   —   المسار:  /admin/includes/auth.php
 *  حماية صفحات لوحة التحكم
 * ============================================================ */
require_once __DIR__ . '/../../config.php';

if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}