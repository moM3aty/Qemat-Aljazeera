<?php
/* ============================================================
 *  index.php   —   المسار:  /index.php
 *  الصفحة الرئيسية (ديناميكية بالكامل)
 * ============================================================ */
require_once __DIR__ . '/config.php';

$services = $pdo->query("SELECT * FROM services WHERE is_active=1 ORDER BY sort_order ASC, id ASC")->fetchAll();
$features = $pdo->query("SELECT * FROM features WHERE is_active=1 ORDER BY sort_order ASC, id ASC")->fetchAll();
$steps    = $pdo->query("SELECT * FROM steps    WHERE is_active=1 ORDER BY sort_order ASC, id ASC")->fetchAll();
$faqs     = $pdo->query("SELECT * FROM faqs     WHERE is_active=1 ORDER BY sort_order ASC, id ASC")->fetchAll();
$gallery  = $pdo->query("SELECT * FROM gallery  ORDER BY sort_order ASC, id ASC")->fetchAll();
$posts    = $pdo->query("SELECT * FROM posts WHERE status='published' ORDER BY created_at DESC LIMIT 3")->fetchAll();

$phone    = setting('phone', '0548203757');
$whatsapp = preg_replace('/\D/', '', setting('whatsapp', '966548203757'));
$wa_link  = 'https://wa.me/' . $whatsapp;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= e(setting('meta_title')) ?></title>
    <meta name="description" content="<?= e(setting('meta_description')) ?>" />
    <meta name="keywords" content="<?= e(setting('meta_keywords')) ?>" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="blog.css">
</head>

<body>

    <!-- ===== Header ===== -->
    <header class="site-header">
        <div class="container header-inner">
            <a href="#top" class="logo">
                <span class="logo-mark"><i class="fa-solid fa-campground"></i></span>
                <span class="logo-text"><?= e(setting('site_name')) ?></span>
            </a>
            <nav class="main-nav" id="mainNav">
                <a href="#top" class="active">الرئيسية</a>
                <a href="#about">من نحن</a>
                <a href="#services">خدماتنا</a>
                <a href="#gallery">معرض الصور</a>
                <a href="blog.php">المدونة</a>
                <a href="#location">موقعنا</a>
                <a href="#contact">اتصل بنا</a>
            </nav>
            <button class="nav-toggle" id="navToggle" aria-label="فتح القائمة">
                <span></span><span></span><span></span>
            </button>
        </div>
    </header>

    <!-- ===== Hero ===== -->
    <section class="hero" id="top">
        <div class="container hero-inner">
            <span class="eyebrow"><?= e(setting('hero_eyebrow')) ?></span>
            <h1><?= e(setting('hero_title')) ?></h1>
            <p><?= e(setting('hero_text')) ?></p>
            <div class="hero-actions">
                <a class="btn btn-call" href="tel:<?= e($phone) ?>">
                    <i class="fa-solid fa-phone"></i> اتصل الآن
                </a>
                <a class="btn btn-whatsapp" href="<?= e($wa_link) ?>" target="_blank" rel="noopener">
                    <i class="fa-brands fa-whatsapp"></i> واتساب
                </a>
            </div>
        </div>
        <div class="diagonal-divider"></div>
    </section>

    <!-- ===== Marquee ===== -->
    <?php if ($services): ?>
    <div class="marquee">
        <div class="marquee-track">
            <?php for ($i = 0; $i < 2; $i++): ?>
                <?php foreach ($services as $s): ?>
                    <span><?= e($s['title']) ?></span>
                <?php endforeach; ?>
            <?php endfor; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- ===== About ===== -->
    <section class="about" id="about">
        <div class="container about-inner">
            <div class="about-photo reveal">
                <img src="<?= e(setting('about_image', 'imgs/img1.jpeg')) ?>" alt="<?= e(setting('site_name')) ?>" loading="lazy" />
            </div>
            <div class="about-text reveal">
                <h2><?= e(setting('about_title', 'من نحن')) ?></h2>
                <p><?= nl2br(e(setting('about_text'))) ?></p>
                <div class="hero-actions">
                    <a class="btn btn-call" href="tel:<?= e($phone) ?>">
                        <i class="fa-solid fa-phone"></i> اتصل الآن
                    </a>
                    <a class="btn btn-whatsapp" href="<?= e($wa_link) ?>" target="_blank" rel="noopener">
                        <i class="fa-brands fa-whatsapp"></i> واتساب
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== Why us ===== -->
    <?php if ($features): ?>
    <section class="why" id="why">
        <div class="container">
            <div class="why-grid">
                <?php foreach ($features as $f): ?>
                <div class="why-item reveal">
                    <i class="<?= e($f['icon']) ?>"></i>
                    <h3><?= e($f['title']) ?></h3>
                    <p><?= e($f['description']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ===== Services ===== -->
    <?php if ($services): ?>
    <section class="services" id="services">
        <div class="container">
            <h2 class="section-title reveal">خدماتنا</h2>
            <p class="section-sub reveal"><?= e(setting('services_sub')) ?></p>

            <div class="service-cards">
                <?php foreach ($services as $s): ?>
                <article class="card reveal">
                    <div class="card-icon"><i class="<?= e($s['icon']) ?>"></i></div>
                    <h3><?= e($s['title']) ?></h3>
                    <p><?= e($s['description']) ?></p>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ===== Gallery ===== -->
    <?php if ($gallery): ?>
    <section class="gallery" id="gallery">
        <div class="container">
            <h2 class="section-title reveal"><?= e(setting('gallery_title', 'معرض الصور')) ?></h2>
            <p class="section-sub reveal"><?= e(setting('gallery_sub')) ?></p>
            <div class="gallery-grid">
                <?php foreach ($gallery as $g): ?>
                    <figure class="reveal">
                        <img src="<?= e($g['image']) ?>" alt="<?= e($g['title']) ?>" loading="lazy">
                    </figure>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ===== Location ===== -->
    <section class="location" id="location">
        <div class="container">
            <h2 class="section-title reveal"><?= e(setting('location_title', 'موقعنا')) ?></h2>
            <p class="section-sub reveal"><?= e(setting('location_text')) ?></p>
            <?php if (setting('map_embed')): ?>
            <div class="map-frame reveal">
                <iframe src="<?= e(setting('map_embed')) ?>"
                    loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen
                    title="موقع <?= e(setting('site_name')) ?> على الخريطة">
                </iframe>
            </div>
            <?php endif; ?>
            <?php if (setting('map_link')): ?>
            <div class="map-link">
                <a href="<?= e(setting('map_link')) ?>" target="_blank" rel="noopener" class="btn btn-outline">
                    <i class="fa-solid fa-location-dot"></i> افتح في خرائط جوجل
                </a>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ===== Booking steps ===== -->
    <?php if ($steps): ?>
    <section class="steps">
        <div class="container">
            <h2 class="section-title reveal"><?= e(setting('steps_title', 'خطوات الحجز')) ?></h2>
            <p class="section-sub reveal"><?= e(setting('steps_sub')) ?></p>
            <div class="steps-grid">
                <?php foreach ($steps as $i => $st): ?>
                <div class="step-item reveal">
                    <span class="step-num"><?= $i + 1 ?></span>
                    <h3><?= e($st['title']) ?></h3>
                    <p><?= e($st['description']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ===== Blog ===== -->
    <?php if ($posts): ?>
    <section class="blog-home" id="blog">
        <div class="container">
            <h2 class="section-title reveal">المدونة</h2>
            <p class="section-sub reveal"><?= e(setting('blog_sub')) ?></p>
            <div class="blog-grid">
                <?php foreach ($posts as $p): ?>
                <article class="blog-card reveal">
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
            <div class="blog-all">
                <a class="btn btn-outline" href="blog.php">كل المقالات</a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ===== FAQ ===== -->
    <?php if ($faqs): ?>
    <section class="faq">
        <div class="container">
            <h2 class="section-title reveal">الأسئلة الشائعة</h2>
            <div class="faq-list">
                <?php foreach ($faqs as $q): ?>
                <details class="reveal">
                    <summary><?= e($q['question']) ?></summary>
                    <p><?= e($q['answer']) ?></p>
                </details>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ===== Contact CTA ===== -->
    <section class="contact-cta" id="contact">
        <div class="diagonal-divider divider-top"></div>
        <div class="container reveal">
            <h2><?= e(setting('cta_title')) ?></h2>
            <p><?= e(setting('cta_text')) ?></p>
            <div class="hero-actions">
                <a class="btn btn-call" href="tel:<?= e($phone) ?>">
                    <i class="fa-solid fa-phone"></i> اتصل الآن
                </a>
                <a class="btn btn-whatsapp" href="<?= e($wa_link) ?>" target="_blank" rel="noopener">
                    <i class="fa-brands fa-whatsapp"></i> واتساب
                </a>
            </div>
        </div>
    </section>

    <!-- ===== Footer ===== -->
    <footer class="site-footer">
        <div class="container footer-grid">
            <div>
                <h3>خدماتنا</h3>
                <ul>
                    <?php foreach (array_slice($services, 0, 5) as $s): ?>
                        <li><a href="#services"><?= e($s['title']) ?></a></li>
                    <?php endforeach; ?>
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