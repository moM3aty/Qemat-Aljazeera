<?php
/* ============================================================
 *  admin/includes/header.php   —   المسار:  /admin/includes/header.php
 * ============================================================ */
$page_title  = $page_title ?? 'لوحة التحكم';
$active      = $active ?? '';
$currentType = $_GET['type'] ?? '';

function nav_active($key, $active, $currentType = '')
{
    return $key === $active ? ' class="active"' : '';
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> | لوحة التحكم</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Quill Rich Text Editor -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">

    <link rel="stylesheet" href="css/admin.css">
</head>

<body>
<div class="admin-shell">

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <span class="brand-mark"><i class="fa-solid fa-campground"></i></span>
            <div>
                <strong>لوحة التحكم</strong>
                <small><?= e(setting('site_name')) ?></small>
            </div>
        </div>

        <nav class="side-nav">
            <a href="index.php"<?= nav_active('dashboard', $active) ?>>
                <i class="fa-solid fa-gauge-high"></i> الرئيسية
            </a>
            <a href="posts.php"<?= nav_active('posts', $active) ?>>
                <i class="fa-solid fa-newspaper"></i> المدونة
            </a>
            <a href="gallery.php"<?= nav_active('gallery', $active) ?>>
                <i class="fa-regular fa-images"></i> معرض الصور
            </a>

            <span class="nav-sep">محتوى الموقع</span>

            <a href="items.php?type=services"<?= ($active === 'items' && $currentType === 'services') ? ' class="active"' : '' ?>>
                <i class="fa-solid fa-briefcase"></i> الخدمات
            </a>
            <a href="items.php?type=features"<?= ($active === 'items' && $currentType === 'features') ? ' class="active"' : '' ?>>
                <i class="fa-solid fa-star"></i> لماذا نحن
            </a>
            <a href="items.php?type=steps"<?= ($active === 'items' && $currentType === 'steps') ? ' class="active"' : '' ?>>
                <i class="fa-solid fa-list-ol"></i> خطوات الحجز
            </a>
            <a href="items.php?type=faqs"<?= ($active === 'items' && $currentType === 'faqs') ? ' class="active"' : '' ?>>
                <i class="fa-regular fa-circle-question"></i> الأسئلة الشائعة
            </a>

            <span class="nav-sep">النظام</span>

            <a href="settings.php"<?= nav_active('settings', $active) ?>>
                <i class="fa-solid fa-gear"></i> إعدادات الموقع
            </a>
            <a href="account.php"<?= nav_active('account', $active) ?>>
                <i class="fa-solid fa-user-shield"></i> حسابي
            </a>
            <a href="logout.php" class="danger" data-logout>
                <i class="fa-solid fa-right-from-bracket"></i> تسجيل الخروج
            </a>
        </nav>
    </aside>

    <main class="main">
        <header class="topbar">
            <button class="burger" id="burger" aria-label="القائمة">
                <i class="fa-solid fa-bars"></i>
            </button>
            <h1 class="topbar-title"><?= e($page_title) ?></h1>
            <div class="topbar-actions">
                <a href="../index.php" target="_blank" class="btn-ghost">
                    <i class="fa-solid fa-eye"></i> عرض الموقع
                </a>
                <span class="user-chip">
                    <i class="fa-solid fa-user"></i>
                    <?= e($_SESSION['admin_username'] ?? 'admin') ?>
                </span>
            </div>
        </header>

        <div class="content">