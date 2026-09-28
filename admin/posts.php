<?php
/* ============================================================
 *  admin/posts.php   —   المسار:  /admin/posts.php
 *  إدارة مقالات المدونة (عرض / حذف / نشر)
 * ============================================================ */
require_once __DIR__ . '/includes/auth.php';

$page_title = 'المدونة';
$active     = 'posts';

/* ---- حذف ---- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);

    $st = $pdo->prepare("SELECT image FROM posts WHERE id = ?");
    $st->execute([$id]);
    if ($row = $st->fetch()) {
        delete_uploaded($row['image']);
    }

    $pdo->prepare("DELETE FROM posts WHERE id = ?")->execute([$id]);
    flash('تم حذف المقال بنجاح.');
    header('Location: posts.php');
    exit;
}

/* ---- تغيير الحالة ---- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle') {
    csrf_check();
    $id = (int)($_POST['id'] ?? 0);
    $pdo->prepare("UPDATE posts SET status = IF(status='published','draft','published') WHERE id = ?")->execute([$id]);
    flash('تم تحديث حالة المقال.');
    header('Location: posts.php');
    exit;
}

$posts = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC")->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="toolbar">
    <a href="post-edit.php" class="btn-gold"><i class="fa-solid fa-plus"></i> مقال جديد</a>
</div>

<div class="panel">
    <?php if ($posts): ?>
    <table class="table">
        <thead>
            <tr>
                <th style="width:70px">الصورة</th>
                <th>العنوان</th>
                <th>الحالة</th>
                <th>المشاهدات</th>
                <th>التاريخ</th>
                <th style="width:170px">إجراءات</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($posts as $p): ?>
            <tr>
                <td>
                    <img class="thumb" src="../<?= e($p['image'] ?: 'imgs/img1.jpeg') ?>" alt="">
                </td>
                <td>
                    <strong><?= e($p['title']) ?></strong>
                    <div class="sub"><?= e(mb_substr($p['excerpt'], 0, 70)) ?>...</div>
                </td>
                <td>
                    <span class="badge <?= $p['status'] === 'published' ? 'badge-on' : 'badge-off' ?>">
                        <?= $p['status'] === 'published' ? 'منشور' : 'مسودة' ?>
                    </span>
                </td>
                <td><?= (int)$p['views'] ?></td>
                <td><?= date('Y/m/d', strtotime($p['created_at'])) ?></td>
                <td class="actions">
                    <a class="icon-btn" href="post-edit.php?id=<?= (int)$p['id'] ?>" title="تعديل">
                        <i class="fa-solid fa-pen"></i>
                    </a>
                    <a class="icon-btn" href="../post.php?slug=<?= urlencode($p['slug']) ?>" target="_blank" title="عرض">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                    <form method="post" style="display:inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="toggle">
                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                        <button class="icon-btn" title="تغيير الحالة"><i class="fa-solid fa-toggle-on"></i></button>
                    </form>
                    <form method="post" style="display:inline" data-confirm="هل أنت متأكد من حذف هذا المقال؟">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                        <button class="icon-btn danger" title="حذف"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p class="empty">لا توجد مقالات بعد. اضغط "مقال جديد" لإضافة أول مقال.</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>