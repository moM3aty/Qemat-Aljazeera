<?php
/* ============================================================
 *  admin/settings.php   —   المسار:  /admin/settings.php
 *  إعدادات الموقع (رقم التواصل، النصوص، الصور، الخريطة ...)
 * ============================================================ */
require_once __DIR__ . '/includes/auth.php';

$page_title = 'إعدادات الموقع';
$active     = 'settings';

/* ====== مجموعات الإعدادات ====== */
$GROUPS = [
    'general' => [
        'label'  => 'الإعدادات العامة',
        'icon'   => 'fa-solid fa-globe',
        'fields' => [
            'site_name'        => ['label' => 'اسم الموقع',        'type' => 'text'],
            'meta_title'       => ['label' => 'عنوان الصفحة (Title)', 'type' => 'text'],
            'meta_description' => ['label' => 'وصف الموقع (Description)', 'type' => 'textarea'],
            'meta_keywords'    => ['label' => 'الكلمات المفتاحية', 'type' => 'textarea'],
            'copyright'        => ['label' => 'حقوق النشر',        'type' => 'text'],
            'designer_name'    => ['label' => 'اسم المصمم',        'type' => 'text'],
            'designer_link'    => ['label' => 'رابط المصمم',       'type' => 'text'],
        ],
    ],
    'contact' => [
        'label'  => 'بيانات التواصل',
        'icon'   => 'fa-solid fa-phone',
        'fields' => [
            'phone'         => ['label' => 'رقم الجوال (مثال: 0548203757)', 'type' => 'text'],
            'whatsapp'      => ['label' => 'رقم واتساب بالصيغة الدولية (مثال: 966548203757)', 'type' => 'text'],
            'address'       => ['label' => 'العنوان الكامل',   'type' => 'text'],
            'address_short' => ['label' => 'العنوان المختصر (الفوتر)', 'type' => 'text'],
            'map_embed'     => ['label' => 'رابط خريطة جوجل (Embed)', 'type' => 'textarea'],
            'map_link'      => ['label' => 'رابط فتح الموقع في خرائط جوجل', 'type' => 'text'],
        ],
    ],
    'hero' => [
        'label'  => 'الواجهة الرئيسية',
        'icon'   => 'fa-solid fa-house',
        'fields' => [
            'hero_eyebrow' => ['label' => 'النص العلوي الصغير', 'type' => 'text'],
            'hero_title'   => ['label' => 'العنوان الرئيسي',    'type' => 'text'],
            'hero_text'    => ['label' => 'النص التعريفي',      'type' => 'textarea'],
            'cta_title'    => ['label' => 'عنوان قسم التواصل',  'type' => 'text'],
            'cta_text'     => ['label' => 'نص قسم التواصل',     'type' => 'textarea'],
        ],
    ],
    'about' => [
        'label'  => 'من نحن',
        'icon'   => 'fa-solid fa-circle-info',
        'fields' => [
            'about_title' => ['label' => 'العنوان', 'type' => 'text'],
            'about_text'  => ['label' => 'النص',    'type' => 'textarea'],
        ],
    ],
    'sections' => [
        'label'  => 'نصوص الأقسام',
        'icon'   => 'fa-solid fa-layer-group',
        'fields' => [
            'services_sub'  => ['label' => 'وصف قسم الخدمات',        'type' => 'textarea'],
            'gallery_title' => ['label' => 'عنوان قسم المعرض',       'type' => 'text'],
            'gallery_sub'   => ['label' => 'وصف قسم المعرض',         'type' => 'textarea'],
            'steps_title'   => ['label' => 'عنوان قسم خطوات الحجز',  'type' => 'text'],
            'steps_sub'     => ['label' => 'وصف قسم خطوات الحجز',    'type' => 'textarea'],
            'blog_sub'      => ['label' => 'وصف قسم المدونة',        'type' => 'textarea'],
            'location_title'=> ['label' => 'عنوان قسم الموقع',       'type' => 'text'],
            'location_text' => ['label' => 'نص قسم الموقع',          'type' => 'textarea'],
            'footer_about'  => ['label' => 'نص الفوتر',              'type' => 'textarea'],
        ],
    ],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $st = $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES (?, ?)
                         ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");

    foreach ($GROUPS as $g) {
        foreach ($g['fields'] as $key => $meta) {
            if (isset($_POST[$key])) {
                $st->execute([$key, trim($_POST[$key])]);
            }
        }
    }

    /* صورة قسم "من نحن" */
    if (!empty($_FILES['about_image_file']['name'])) {
        $uploaded = upload_image($_FILES['about_image_file'], 'about');
        if ($uploaded) {
            delete_uploaded(setting('about_image'));
            $st->execute(['about_image', $uploaded]);
        } else {
            flash('تعذر رفع صورة "من نحن".', 'error');
            header('Location: settings.php');
            exit;
        }
    } elseif (isset($_POST['about_image']) && trim($_POST['about_image']) !== '') {
        $st->execute(['about_image', trim($_POST['about_image'])]);
    }

    flash('تم حفظ الإعدادات بنجاح.');
    header('Location: settings.php');
    exit;
}

include __DIR__ . '/includes/header.php';
?>

<form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <?php foreach ($GROUPS as $gkey => $group): ?>
    <div class="panel">
        <div class="panel-head">
            <h2><i class="<?= e($group['icon']) ?>"></i> <?= e($group['label']) ?></h2>
        </div>

        <div class="form-grid">
            <?php foreach ($group['fields'] as $key => $meta): ?>
                <?php if ($meta['type'] === 'textarea'): ?>
                    <label class="field col-2">
                        <span><?= e($meta['label']) ?></span>
                        <textarea name="<?= $key ?>" rows="3"><?= e(setting($key)) ?></textarea>
                    </label>
                <?php else: ?>
                    <label class="field">
                        <span><?= e($meta['label']) ?></span>
                        <input type="text" name="<?= $key ?>" value="<?= e(setting($key)) ?>">
                    </label>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if ($gkey === 'about'): ?>
                <label class="field">
                    <span>رابط صورة قسم "من نحن"</span>
                    <input type="text" name="about_image" value="<?= e(setting('about_image')) ?>">
                </label>
                <label class="field">
                    <span>أو ارفع صورة جديدة</span>
                    <input type="file" name="about_image_file" accept="image/*">
                </label>
                <div class="field col-2">
                    <div class="preview-box">
                        <img src="../<?= e(setting('about_image')) ?>" alt="">
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>

    <div class="sticky-save">
        <button type="submit" class="btn-gold"><i class="fa-solid fa-floppy-disk"></i> حفظ كل الإعدادات</button>
    </div>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>