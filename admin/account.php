<?php
/* ============================================================
 *  admin/account.php   —   المسار:  /admin/account.php
 *  تعديل بيانات الدخول (اسم المستخدم / كلمة المرور)
 * ============================================================ */
require_once __DIR__ . '/includes/auth.php';

$page_title = 'حسابي';
$active     = 'account';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $username = trim($_POST['username'] ?? '');
    $current  = $_POST['current_password'] ?? '';
    $new      = $_POST['new_password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    $st = $pdo->prepare("SELECT * FROM admins WHERE id = ? LIMIT 1");
    $st->execute([$_SESSION['admin_id']]);
    $admin = $st->fetch();

    if (!$admin || !password_verify($current, $admin['password'])) {
        $errors[] = 'كلمة المرور الحالية غير صحيحة.';
    }
    if ($username === '') {
        $errors[] = 'اسم المستخدم مطلوب.';
    }
    if ($new !== '' && strlen($new) < 6) {
        $errors[] = 'كلمة المرور الجديدة يجب أن تكون 6 أحرف على الأقل.';
    }
    if ($new !== $confirm) {
        $errors[] = 'تأكيد كلمة المرور غير مطابق.';
    }

    if (!$errors) {
        if ($new !== '') {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE admins SET username = ?, password = ? WHERE id = ?")
                ->execute([$username, $hash, $admin['id']]);
            flash('تم تحديث اسم المستخدم وكلمة المرور.');
        } else {
            $pdo->prepare("UPDATE admins SET username = ? WHERE id = ?")
                ->execute([$username, $admin['id']]);
            flash('تم تحديث اسم المستخدم.');
        }
        $_SESSION['admin_username'] = $username;
        header('Location: account.php');
        exit;
    }
}

include __DIR__ . '/includes/header.php';
?>

<?php if ($errors): ?>
    <div class="alert alert-error">
        <i class="fa-solid fa-circle-exclamation"></i>
        <ul style="margin:0;padding-inline-start:18px">
            <?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" class="panel form-panel">
    <?= csrf_field() ?>

    <div class="form-grid">
        <label class="field">
            <span>اسم المستخدم</span>
            <input type="text" name="username" required value="<?= e($_SESSION['admin_username']) ?>">
        </label>

        <label class="field">
            <span>كلمة المرور الحالية *</span>
            <input type="password" name="current_password" required>
        </label>

        <label class="field">
            <span>كلمة المرور الجديدة (اتركها فارغة لعدم التغيير)</span>
            <input type="password" name="new_password">
        </label>

        <label class="field">
            <span>تأكيد كلمة المرور الجديدة</span>
            <input type="password" name="confirm_password">
        </label>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn-gold"><i class="fa-solid fa-floppy-disk"></i> حفظ التعديلات</button>
    </div>
</form>

<?php include __DIR__ . '/includes/footer.php'; ?>