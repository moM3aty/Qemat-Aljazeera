<?php
/* ============================================================
 *  admin/gallery.php   —   المسار:  /admin/gallery.php
 *  إدارة معرض الصور (رفع / تعديل / حذف)
 * ============================================================ */
require_once __DIR__ . '/includes/auth.php';

$page_title = 'معرض الصور';
$active     = 'gallery';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';

    /* ---------- رفع صور جديدة ---------- */
    if ($action === 'upload') {
        $title   = trim($_POST['title'] ?? '');
        $count   = 0;
        $failed  = 0;

        if (!empty($_FILES['images']['name'][0])) {
            $files = $_FILES['images'];
            $total = count($files['name']);

            for ($i = 0; $i < $total; $i++) {
                $one = [
                    'name'     => $files['name'][$i],
                    'type'     => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error'    => $files['error'][$i],
                    'size'     => $files['size'][$i],
                ];
                $path = upload_image($one, 'gallery');
                if ($path) {
                    $st = $pdo->prepare("INSERT INTO gallery (image, title, sort_order) VALUES (?,?,?)");
                    $st->execute([$path, $title, 999]);
                    $count++;
                } else {
                    $failed++;
                }
            }
        }

        if ($count > 0) {
            flash("تم رفع {$count} صورة بنجاح." . ($failed ? " وفشل رفع {$failed} ملف." : ''));
        } else {
            flash('لم يتم رفع أي صورة. تأكد من اختيار ملفات صور صحيحة.', 'error');
        }
        header('Location: gallery.php');
        exit;
    }

    /* ---------- تعديل صورة ---------- */
    if ($action === 'update') {
        $id    = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $sort  = (int)($_POST['sort_order'] ?? 0);
        $pdo->prepare("UPDATE gallery SET title = ?, sort_order = ? WHERE id = ?")
            ->execute([$title, $sort, $id]);
        flash('تم تحديث بيانات الصورة.');
        header('Location: gallery.php');
        exit;
    }

    /* ---------- حذف صورة ---------- */
    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $st = $pdo->prepare("SELECT image FROM gallery WHERE id = ?");
        $st->execute([$id]);
        if ($row = $st->fetch()) {
            delete_uploaded($row['image']);
        }
        $pdo->prepare("DELETE FROM gallery WHERE id = ?")->execute([$id]);
        flash('تم حذف الصورة.');
        header('Location: gallery.php');
        exit;
    }
}

$images = $pdo->query("SELECT * FROM gallery ORDER BY sort_order ASC, id ASC")->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="panel">
    <div class="panel-head">
        <h2><i class="fa-solid fa-cloud-arrow-up"></i> رفع صور جديدة</h2>
    </div>

    <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="upload">

        <div class="form-grid">
            <label class="field">
                <span>وصف / عنوان الصور (اختياري)</span>
                <input type="text" name="title" placeholder="مثال: بيوت شعر">
            </label>

            <label class="field">
                <span>اختر الصور (يمكن اختيار أكثر من صورة)</span>
                <input type="file" name="images[]" accept="image/*" multiple required data-preview="#upPreview">
            </label>

            <div class="field col-2">
                <div id="upPreview" class="preview-box"></div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-gold"><i class="fa-solid fa-upload"></i> رفع الصور</button>
        </div>
    </form>
</div>

<div class="panel">
    <div class="panel-head">
        <h2><i class="fa-regular fa-images"></i> صور المعرض (<?= count($images) ?>)</h2>
    </div>

    <?php if ($images): ?>
    <div class="gallery-admin">
        <?php foreach ($images as $img): ?>
        <div class="gallery-item">
            <div class="gi-thumb">
                <img src="../<?= e($img['image']) ?>" alt="<?= e($img['title']) ?>">
            </div>

            <form method="post" class="gi-form">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?= (int)$img['id'] ?>">

                <input type="text" name="title" value="<?= e($img['title']) ?>" placeholder="الوصف">
                <input type="number" name="sort_order" value="<?= (int)$img['sort_order'] ?>" title="الترتيب" class="sort-input">

                <div class="gi-actions">
                    <button class="icon-btn" title="حفظ"><i class="fa-solid fa-floppy-disk"></i></button>
                </div>
            </form>

            <form method="post" data-confirm="هل أنت متأكد من حذف هذه الصورة؟" class="gi-delete">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$img['id'] ?>">
                <button class="icon-btn danger" title="حذف"><i class="fa-solid fa-trash"></i></button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
        <p class="empty">لا توجد صور في المعرض.</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>