<?php
/* ============================================================
 *  admin/index.php   —   المسار:  /admin/index.php
 *  الصفحة الرئيسية للوحة التحكم
 * ============================================================ */
require_once __DIR__ . '/includes/auth.php';

$page_title = 'الرئيسية';
$active     = 'dashboard';

$stats = [
    'posts'    => (int)$pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn(),
    'gallery'  => (int)$pdo->query("SELECT COUNT(*) FROM gallery")->fetchColumn(),
    'services' => (int)$pdo->query("SELECT COUNT(*) FROM services")->fetchColumn(),
    'faqs'     => (int)$pdo->query("SELECT COUNT(*) FROM faqs")->fetchColumn(),
];

$latestPosts = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC LIMIT 5")->fetchAll();
$latestImgs  = $pdo->query("SELECT * FROM gallery ORDER BY id DESC LIMIT 8")->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="cards-grid">
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-newspaper"></i></div>
        <div>
            <span class="stat-num"><?= $stats['posts'] ?></span>
            <span class="stat-label">مقال في المدونة</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-regular fa-images"></i></div>
        <div>
            <span class="stat-num"><?= $stats['gallery'] ?></span>
            <span class="stat-label">صورة في المعرض</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-solid fa-briefcase"></i></div>
        <div>
            <span class="stat-num"><?= $stats['services'] ?></span>
            <span class="stat-label">خدمة</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon"><i class="fa-regular fa-circle-question"></i></div>
        <div>
            <span class="stat-num"><?= $stats['faqs'] ?></span>
            <span class="stat-label">سؤال شائع</span>
        </div>
    </div>
</div>

<div class="quick-actions">
    <a href="post-edit.php" class="btn-gold"><i class="fa-solid fa-plus"></i> مقال جديد</a>
    <a href="gallery.php" class="btn-ghost"><i class="fa-solid fa-upload"></i> رفع صور</a>
    <a href="settings.php" class="btn-ghost"><i class="fa-solid fa-phone"></i> تعديل رقم التواصل</a>
</div>

<div class="panel-grid">
    <div class="panel">
        <div class="panel-head">
            <h2><i class="fa-solid fa-clock-rotate-left"></i> أحدث المقالات</h2>
            <a href="posts.php" class="link-more">عرض الكل</a>
        </div>
        <?php if ($latestPosts): ?>
            <table class="table">
                <thead>
                    <tr><th>العنوان</th><th>الحالة</th><th>التاريخ</th><th></th></tr>
                </thead>
                <tbody>
                <?php foreach ($latestPosts as $p): ?>
                    <tr>
                        <td><?= e($p['title']) ?></td>
                        <td>
                            <span class="badge <?= $p['status'] === 'published' ? 'badge-on' : 'badge-off' ?>">
                                <?= $p['status'] === 'published' ? 'منشور' : 'مسودة' ?>
                            </span>
                        </td>
                        <td><?= date('Y/m/d', strtotime($p['created_at'])) ?></td>
                        <td><a class="icon-btn" href="post-edit.php?id=<?= (int)$p['id'] ?>"><i class="fa-solid fa-pen"></i></a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="empty">لا توجد مقالات بعد.</p>
        <?php endif; ?>
    </div>

    <div class="panel">
        <div class="panel-head">
            <h2><i class="fa-regular fa-images"></i> أحدث الصور</h2>
            <a href="gallery.php" class="link-more">إدارة المعرض</a>
        </div>
        <div class="mini-gallery">
            <?php foreach ($latestImgs as $g): ?>
                <img src="../<?= e($g['image']) ?>" alt="<?= e($g['title']) ?>">
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>