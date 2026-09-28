<?php
/* ============================================================
 *  admin/items.php   —   المسار:  /admin/items.php
 *  إدارة: الخدمات / لماذا نحن / خطوات الحجز / الأسئلة الشائعة
 *  الاستخدام: items.php?type=services
 * ============================================================ */
require_once __DIR__ . '/includes/auth.php';

/* ====== الأنواع المسموح بها ====== */
$TYPES = [
    'services' => [
        'label'  => 'الخدمات',
        'single' => 'خدمة',
        'icon'   => 'fa-solid fa-briefcase',
        'fields' => [
            'icon'        => ['label' => 'الأيقونة (Font Awesome)', 'type' => 'text',     'hint' => 'مثال: fa-solid fa-campground'],
            'title'       => ['label' => 'العنوان',                  'type' => 'text'],
            'description' => ['label' => 'الوصف',                    'type' => 'textarea'],
        ],
    ],
    'features' => [
        'label'  => 'لماذا نحن',
        'single' => 'ميزة',
        'icon'   => 'fa-solid fa-star',
        'fields' => [
            'icon'        => ['label' => 'الأيقونة (Font Awesome)', 'type' => 'text',     'hint' => 'مثال: fa-solid fa-truck-fast'],
            'title'       => ['label' => 'العنوان',                  'type' => 'text'],
            'description' => ['label' => 'الوصف',                    'type' => 'textarea'],
        ],
    ],
    'steps' => [
        'label'  => 'خطوات الحجز',
        'single' => 'خطوة',
        'icon'   => 'fa-solid fa-list-ol',
        'fields' => [
            'title'       => ['label' => 'العنوان', 'type' => 'text'],
            'description' => ['label' => 'الوصف',   'type' => 'textarea'],
        ],
    ],
    'faqs' => [
        'label'  => 'الأسئلة الشائعة',
        'single' => 'سؤال',
        'icon'   => 'fa-regular fa-circle-question',
        'fields' => [
            'question' => ['label' => 'السؤال',  'type' => 'text'],
            'answer'   => ['label' => 'الإجابة', 'type' => 'textarea'],
        ],
    ],
];

$type = $_GET['type'] ?? 'services';
if (!isset($TYPES[$type])) {
    $type = 'services';
}

$cfg   = $TYPES[$type];
$table = $type; // أسماء الجداول مطابقة للمفاتيح

$page_title = $cfg['label'];
$active     = 'items';

/* ====== العمليات ====== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id   = (int)($_POST['id'] ?? 0);
        $data = [];

        foreach ($cfg['fields'] as $field => $meta) {
            $data[$field] = trim($_POST[$field] ?? '');
        }
        $data['sort_order'] = (int)($_POST['sort_order'] ?? 0);
        $data['is_active']  = isset($_POST['is_active']) ? 1 : 0;

        if ($id > 0) {
            $set = [];
            foreach (array_keys($data) as $k) {
                $set[] = "`$k` = :$k";
            }
            $data['id'] = $id;
            $sql = "UPDATE `$table` SET " . implode(', ', $set) . " WHERE id = :id";
            $pdo->prepare($sql)->execute($data);
            flash('تم تحديث ' . $cfg['single'] . ' بنجاح.');
        } else {
            $cols = '`' . implode('`, `', array_keys($data)) . '`';
            $ph   = ':' . implode(', :', array_keys($data));
            $pdo->prepare("INSERT INTO `$table` ($cols) VALUES ($ph)")->execute($data);
            flash('تمت إضافة ' . $cfg['single'] . ' بنجاح.');
        }

        header("Location: items.php?type=$type");
        exit;
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("DELETE FROM `$table` WHERE id = ?")->execute([$id]);
        flash('تم الحذف بنجاح.');
        header("Location: items.php?type=$type");
        exit;
    }

    if ($action === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        $pdo->prepare("UPDATE `$table` SET is_active = IF(is_active=1,0,1) WHERE id = ?")->execute([$id]);
        flash('تم تحديث الحالة.');
        header("Location: items.php?type=$type");
        exit;
    }
}

/* ====== بيانات التعديل ====== */
$edit = null;
if (!empty($_GET['edit'])) {
    $st = $pdo->prepare("SELECT * FROM `$table` WHERE id = ? LIMIT 1");
    $st->execute([(int)$_GET['edit']]);
    $edit = $st->fetch();
}

$rows = $pdo->query("SELECT * FROM `$table` ORDER BY sort_order ASC, id ASC")->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="type-tabs">
    <?php foreach ($TYPES as $key => $t): ?>
        <a href="items.php?type=<?= $key ?>" class="<?= $key === $type ? 'active' : '' ?>">
            <i class="<?= e($t['icon']) ?>"></i> <?= e($t['label']) ?>
        </a>
    <?php endforeach; ?>
</div>

<form method="post" class="panel form-panel">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= $edit ? (int)$edit['id'] : 0 ?>">

    <div class="panel-head">
        <h2>
            <i class="fa-solid <?= $edit ? 'fa-pen' : 'fa-plus' ?>"></i>
            <?= $edit ? 'تعديل ' . e($cfg['single']) : 'إضافة ' . e($cfg['single']) . ' جديد' ?>
        </h2>
        <?php if ($edit): ?>
            <a class="link-more" href="items.php?type=<?= $type ?>">إلغاء التعديل</a>
        <?php endif; ?>
    </div>

    <div class="form-grid">
        <?php foreach ($cfg['fields'] as $field => $meta): ?>

            <?php if ($field === 'icon'): ?>
                <?php /* ====== Icon Picker ====== */ ?>
                <div class="field col-2">
                    <span><?= e($meta['label']) ?></span>
                    <div class="icon-picker" data-icon-picker>
                        <div class="icon-picker-preview">
                            <i class="<?= e($edit[$field] ?? 'fa-solid fa-star') ?>"></i>
                        </div>
                        <input type="text" name="<?= $field ?>" class="icon-input"
                               value="<?= e($edit[$field] ?? 'fa-solid fa-star') ?>">
                        <button type="button" class="btn-gold icon-picker-open">
                            <i class="fa-solid fa-icons"></i> اختر أيقونة
                        </button>
                    </div>
                    <small class="hint">اختر من المكتبة أو اكتب اسم الأيقونة مباشرة (مثال: fa-solid fa-tent).</small>
                </div>

            <?php elseif ($meta['type'] === 'textarea'): ?>
                <label class="field col-2">
                    <span><?= e($meta['label']) ?></span>
                    <textarea name="<?= $field ?>" rows="3"><?= e($edit[$field] ?? '') ?></textarea>
                    <?php if (!empty($meta['hint'])): ?><small class="hint"><?= e($meta['hint']) ?></small><?php endif; ?>
                </label>

            <?php else: ?>
                <label class="field">
                    <span><?= e($meta['label']) ?></span>
                    <input type="text" name="<?= $field ?>" value="<?= e($edit[$field] ?? '') ?>">
                    <?php if (!empty($meta['hint'])): ?><small class="hint"><?= e($meta['hint']) ?></small><?php endif; ?>
                </label>
            <?php endif; ?>

        <?php endforeach; ?>

        <label class="field">
            <span>الترتيب</span>
            <input type="number" name="sort_order" value="<?= (int)($edit['sort_order'] ?? (count($rows) + 1)) ?>">
        </label>

        <label class="field checkbox-field">
            <input type="checkbox" name="is_active" value="1" <?= (!$edit || $edit['is_active']) ? 'checked' : '' ?>>
            <span>مُفعّل ويظهر في الموقع</span>
        </label>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-gold"><i class="fa-solid fa-floppy-disk"></i> حفظ</button>
    </div>
</form>

<div class="panel">
    <div class="panel-head">
        <h2><i class="<?= e($cfg['icon']) ?>"></i> <?= e($cfg['label']) ?> (<?= count($rows) ?>)</h2>
    </div>

    <?php if ($rows): ?>
    <table class="table">
        <thead>
            <tr>
                <th style="width:60px">الترتيب</th>
                <?php if (isset($cfg['fields']['icon'])): ?><th style="width:70px">أيقونة</th><?php endif; ?>
                <th>المحتوى</th>
                <th style="width:110px">الحالة</th>
                <th style="width:150px">إجراءات</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= (int)$r['sort_order'] ?></td>
                <?php if (isset($cfg['fields']['icon'])): ?>
                    <td><i class="<?= e($r['icon']) ?>" style="font-size:1.2rem;color:#d4af37"></i></td>
                <?php endif; ?>
                <td>
                    <strong>
                        <?= e($r['title'] ?? $r['question'] ?? '') ?>
                    </strong>
                    <div class="sub"><?= e(mb_substr($r['description'] ?? $r['answer'] ?? '', 0, 80)) ?></div>
                </td>
                <td>
                    <span class="badge <?= $r['is_active'] ? 'badge-on' : 'badge-off' ?>">
                        <?= $r['is_active'] ? 'مُفعّل' : 'مُخفي' ?>
                    </span>
                </td>
                <td class="actions">
                    <a class="icon-btn" href="items.php?type=<?= $type ?>&edit=<?= (int)$r['id'] ?>" title="تعديل">
                        <i class="fa-solid fa-pen"></i>
                    </a>
                    <form method="post" style="display:inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="toggle">
                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                        <button class="icon-btn" title="إظهار / إخفاء"><i class="fa-solid fa-toggle-on"></i></button>
                    </form>
                    <form method="post" style="display:inline" data-confirm="هل أنت متأكد من الحذف؟">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                        <button class="icon-btn danger" title="حذف"><i class="fa-solid fa-trash"></i></button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p class="empty">لا توجد عناصر بعد.</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>