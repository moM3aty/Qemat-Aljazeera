<?php
/* ============================================================
 *  admin/post-edit.php   —   المسار:  /admin/post-edit.php
 *  إضافة / تعديل مقال
 * ============================================================ */
require_once __DIR__ . '/includes/auth.php';

$page_title = 'مقال';
$active     = 'posts';

$id   = (int)($_GET['id'] ?? 0);
$post = [
    'id'         => 0,
    'title'      => '',
    'slug'       => '',
    'excerpt'    => '',
    'content'    => '',
    'image'      => '',
    'status'     => 'published',
];

if ($id > 0) {
    $st = $pdo->prepare("SELECT * FROM posts WHERE id = ? LIMIT 1");
    $st->execute([$id]);
    $found = $st->fetch();
    if (!$found) {
        flash('المقال غير موجود.', 'error');
        header('Location: posts.php');
        exit;
    }
    $post = $found;
    $page_title = 'تعديل مقال';
} else {
    $page_title = 'مقال جديد';
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $post['title']   = trim($_POST['title'] ?? '');
    $post['slug']    = trim($_POST['slug'] ?? '');
    $post['excerpt'] = trim($_POST['excerpt'] ?? '');
    $post['content'] = $_POST['content'] ?? '';
    $post['status']  = ($_POST['status'] ?? 'published') === 'draft' ? 'draft' : 'published';
    $post['image']   = trim($_POST['current_image'] ?? '');

    if ($post['title'] === '') {
        $errors[] = 'عنوان المقال مطلوب.';
    }

    /* رفع صورة جديدة */
    if (!empty($_FILES['image']['name'])) {
        $uploaded = upload_image($_FILES['image'], 'post');
        if ($uploaded) {
            delete_uploaded($post['image']);
            $post['image'] = $uploaded;
        } else {
            $errors[] = 'تعذر رفع الصورة. تأكد من أن الملف صورة (jpg, png, webp, gif) وحجمه أقل من 6 ميجا.';
        }
    }

    if (!$errors) {
        $slug = $post['slug'] !== '' ? make_slug($post['slug']) : make_slug($post['title']);
        $slug = unique_slug($pdo, $slug, (int)$post['id']);

        if ((int)$post['id'] > 0) {
            $st = $pdo->prepare("UPDATE posts SET title=?, slug=?, excerpt=?, content=?, image=?, status=? WHERE id=?");
            $st->execute([$post['title'], $slug, $post['excerpt'], $post['content'], $post['image'], $post['status'], $post['id']]);
            flash('تم تحديث المقال بنجاح.');
        } else {
            $st = $pdo->prepare("INSERT INTO posts (title, slug, excerpt, content, image, status) VALUES (?,?,?,?,?,?)");
            $st->execute([$post['title'], $slug, $post['excerpt'], $post['content'], $post['image'], $post['status']]);
            flash('تمت إضافة المقال بنجاح.');
        }
        header('Location: posts.php');
        exit;
    }
}

include __DIR__ . '/includes/header.php';
?>

<div class="toolbar">
    <a href="posts.php" class="btn-ghost"><i class="fa-solid fa-arrow-right"></i> رجوع للمدونة</a>
</div>

<?php if ($errors): ?>
    <div class="alert alert-error">
        <i class="fa-solid fa-circle-exclamation"></i>
        <ul style="margin:0;padding-inline-start:18px">
            <?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="panel form-panel">
    <?= csrf_field() ?>
    <input type="hidden" name="current_image" value="<?= e($post['image']) ?>">

    <div class="form-grid">
        <label class="field col-2">
            <span>عنوان المقال *</span>
            <input type="text" name="title" required value="<?= e($post['title']) ?>">
        </label>

        <label class="field col-2">
            <span>الرابط (slug) — اتركه فارغاً ليتم إنشاؤه تلقائياً</span>
            <input type="text" name="slug" value="<?= e($post['slug']) ?>" placeholder="best-tent-setup-riyadh">
        </label>

        <label class="field col-2">
            <span>وصف مختصر</span>
            <textarea name="excerpt" rows="3"><?= e($post['excerpt']) ?></textarea>
        </label>

        <label class="field">
            <span>الحالة</span>
            <select name="status">
                <option value="published" <?= $post['status'] === 'published' ? 'selected' : '' ?>>منشور</option>
                <option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>مسودة</option>
            </select>
        </label>

        <label class="field">
            <span>صورة المقال</span>
            <input type="file" name="image" accept="image/*" data-preview="#postPreview">
        </label>

        <div class="field col-2">
            <span>محتوى المقال (Rich Text)</span>

            <div class="quill-wrap">
                <div id="editor"></div>
            </div>

            <!-- المحتوى الفعلي المُرسل (مخفي) -->
            <textarea id="contentInput" name="content" style="display:none"
                      data-placeholder="اكتب محتوى المقال هنا..."><?= e($post['content']) ?></textarea>

            <small class="hint">
                يمكنك التنسيق الكامل: عناوين، قوائم، روابط، صور، ألوان. المحتوى سيظهر منسّقاً في المدونة.
            </small>
        </div>

    <div class="form-actions">
        <button type="submit" class="btn-gold"><i class="fa-solid fa-floppy-disk"></i> حفظ</button>
        <a href="posts.php" class="btn-ghost">إلغاء</a>
    </div>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>