<?php
/* ============================================================
 *  blog.php   —   المسار:  /blog.php
 *  صفحة كل المقالات
 * ============================================================ */
require_once __DIR__ . '/config.php';

$posts    = $pdo->query("SELECT * FROM posts WHERE status='published' ORDER BY created_at DESC")->fetchAll();
$services = $pdo->query("SELECT * FROM services WHERE is_active=1 ORDER BY sort_order ASC, id ASC")->fetchAll();

$phone    = setting('phone', '0548203757');
$whatsapp = preg_replace('/\D/', '', setting('whatsapp', '966548203757'));
$wa_link  = 'https://wa.me/' . $whatsapp;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>المدونة | <?= e(setting('site_name')) ?></title>
    <meta name="description" content="<?= e(setting('meta_description')) ?>" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="blog.css">
</head>

<body>

    <header class="site-header">
        <div class="container header-inner">
            <a href="index.php" class="logo">
                <span class="logo-mark"><i class="fa-solid fa-campground"></i></span>
                <span class="logo-text"><?= e(setting('site_name')) ?></span>
            </a>
            <nav class="main-nav" id="mainNav">
                <a href="index.php">الرئيسية</a>
                <a href="index.php#about">من نحن</a>
                <a href="index.php#services">خدماتنا</a>
                <a href="index.php#gallery">معرض الصور</a>
                <a href="blog.php" class="active">المدونة</a>
                <a href="index.php#location">موقعنا</a>
                <a href="index.php#contact">اتصل بنا</a>
            </nav>
            <button class="nav-toggle" id="navToggle" aria-label="فتح القائمة">
                <span></span><span></span><span></span>
            </button>
        </div>
    </header>

    <section class="page-hero">
        <div class="container">
            <h1>المدونة</h1>
            <p><?= e(setting('blog_sub')) ?></p>
        </div>
    </section>

    <section class="blog-home">
        <div class="container">
            <?php if ($posts): ?>
                <div class="blog-grid">
                    <?php foreach ($posts as $p): ?>
                    <article class="blog-card reveal in-view">
                        <a class="blog-thumb" href="post.php?slug=<?= urlencode($p['slug']) ?>">
                            <img src="<?= e($p['image'] ?: 'imgs/img1.jpeg') ?>" alt="<?= e($p['title']) ?>" loading="lazy">
                        </a>
                        <div class="blog-body">
                            <span class="blog-date">
                                <i class="fa-regular fa-calendar"></i>
                                <?= date('Y/m/d', strtotime($p['created_at'])) ?>
                            </span>
                            <h3><a href="post.php?slug=<?= urlencode($p['slug']) ?>"><?= e($p['title']) ?></a></h3>
                            <p><?= e($p['excerpt']) ?></p>
                            <a class="blog-more" href="post.php?slug=<?= urlencode($p['slug']) ?>">
                                اقرأ المزيد <i class="fa-solid fa-arrow-left"></i>
                            </a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="text-align:center;color:#6b5f5a">لا توجد مقالات منشورة حتى الآن.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- ===== Footer ===== -->
    <footer class="site-footer">
        <div class="container footer-grid">
            <div>
                <h3>خدماتنا</h3>
                <ul>
                    <?php if ($services): ?>
                        <?php foreach (array_slice($services, 0, 5) as $s): ?>
                            <li><a href="index.php#services"><?= e($s['title']) ?></a></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li><a href="index.php#services">خدماتنا</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div>
                <h3>معلومات التواصل</h3>
                <p>العنوان: <?= e(setting('address_short')) ?></p>
                <p>للتواصل: <a href="tel:<?= e($phone) ?>"><?= e($phone) ?></a></p>
                <a class="footer-social" href="<?= e($wa_link) ?>" target="_blank" rel="noopener" title="واتساب">
                    <i class="fa-brands fa-whatsapp"></i>
                </a>
            </div>
            <div class="footer-brand">
                <span class="logo-mark"><i class="fa-solid fa-campground"></i></span>
                <p><?= e(setting('footer_about')) ?></p>
            </div>
        </div>
        <div class="footer-bottom">
            <p><?= e(setting('copyright')) ?></p>
            <p class="designer-credit">
                تصميم وتنفيذ
                <a href="<?= e(setting('designer_link')) ?>" target="_blank" style="text-decoration: none;">
                    <span class="gmt-text" style="color: var(--gold); font-weight: bold;"><?= e(setting('designer_name')) ?></span>
                </a>
            </p>
        </div>
    </footer>

    <!-- ===== Floating buttons ===== -->
    <a href="<?= e($wa_link) ?>" target="_blank" rel="noopener" class="float-btn float-whatsapp" aria-label="واتساب">
        <i class="fa-brands fa-whatsapp"></i>
    </a>
    <a href="tel:<?= e($phone) ?>" class="float-btn float-call" aria-label="اتصال">
        <i class="fa-solid fa-phone"></i>
    </a>

    <script src="script.js"></script>
</body>

</html>