<?php
/* ============================================================
 *  admin/login.php   —   المسار:  /admin/login.php
 *  تسجيل دخول لوحة التحكم — تصميم Premium
 * ============================================================ */
require_once __DIR__ . '/../config.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'يرجى إدخال اسم المستخدم وكلمة المرور.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id']       = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'اسم المستخدم أو كلمة المرور غير صحيحة.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول | لوحة التحكم</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="css/login.css">
</head>

<body class="login-body">

    <div class="login-shell">

        <!-- ==================== الجهة اليمنى: العلامة التجارية ==================== -->
        <aside class="login-brand">
            <div class="brand-shapes">
                <span class="shape shape-1"></span>
                <span class="shape shape-2"></span>
                <span class="shape shape-3"></span>
            </div>

            <div class="brand-content">
                <div class="brand-logo">
                    <span class="brand-mark"><i class="fa-solid fa-campground"></i></span>
                    <span class="brand-name"><?= e(setting('site_name')) ?></span>
                </div>

                <h1 class="brand-title">
                    لوحة <span class="grad">التحكم</span>
                </h1>

                <p class="brand-desc">
                    إدارة كاملة للموقع من مكان واحد — المدونة، معرض الصور، الخدمات،
                    وإعدادات التواصل بكل سهولة.
                </p>

                <ul class="brand-features">
                    <li>
                        <span class="feature-icon"><i class="fa-solid fa-newspaper"></i></span>
                        <div>
                            <strong>المدونة</strong>
                            <small>أضف وعدّل المقالات بمحرر غني</small>
                        </div>
                    </li>
                    <li>
                        <span class="feature-icon"><i class="fa-regular fa-images"></i></span>
                        <div>
                            <strong>معرض الصور</strong>
                            <small>ارفع صور أعمالك بأزرار بسيطة</small>
                        </div>
                    </li>
                    <li>
                        <span class="feature-icon"><i class="fa-solid fa-sliders"></i></span>
                        <div>
                            <strong>إعدادات الموقع</strong>
                            <small>تحكم في الأرقام والنصوص والخريطة</small>
                        </div>
                    </li>
                </ul>

                <div class="brand-footer">
                    <span>© <?= date('Y') ?> <?= e(setting('site_name')) ?></span>
                </div>
            </div>
        </aside>

        <!-- ==================== الجهة اليسرى: الفورم ==================== -->
        <main class="login-form-side">
            <div class="form-wrap">

                <div class="form-head">
                    <span class="hello-badge">
                        <i class="fa-solid fa-hand-sparkles"></i> مرحباً بك
                    </span>
                    <h2>تسجيل الدخول</h2>
                    <p>أدخل بياناتك للوصول إلى لوحة التحكم</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert-inline" id="loginError" data-msg="<?= e($error) ?>">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <?= e($error) ?>
                    </div>
                <?php endif; ?>

                <form method="post" autocomplete="off" id="loginForm">
                    <?= csrf_field() ?>

                    <label class="form-field">
                        <span class="field-label">اسم المستخدم</span>
                        <div class="input-wrap">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" name="username" id="inpUser" required autofocus
                                   placeholder="اكتب اسم المستخدم"
                                   value="<?= e($_POST['username'] ?? '') ?>">
                        </div>
                    </label>

                    <label class="form-field">
                        <span class="field-label">كلمة المرور</span>
                        <div class="input-wrap">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" name="password" id="inpPass" required
                                   placeholder="••••••••">
                            <button type="button" class="toggle-pass" id="togglePass" aria-label="إظهار كلمة المرور">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </label>

                    <button type="submit" class="btn-submit">
                        <span>دخول إلى لوحة التحكم</span>
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>

                    <a class="back-site" href="../index.php">
                        <i class="fa-solid fa-arrow-right"></i> العودة إلى الموقع
                    </a>
                </form>

                <p class="form-note">
                    <i class="fa-solid fa-shield-halved"></i>
                    محمي بواسطة تشفير كامل للبيانات
                </p>
            </div>
        </main>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    (function () {
        /* ---------- إظهار / إخفاء كلمة المرور ---------- */
        const toggle = document.getElementById('togglePass');
        const pass   = document.getElementById('inpPass');
        if (toggle && pass) {
            toggle.addEventListener('click', function () {
                const show = pass.type === 'password';
                pass.type = show ? 'text' : 'password';
                toggle.innerHTML = show
                    ? '<i class="fa-regular fa-eye-slash"></i>'
                    : '<i class="fa-regular fa-eye"></i>';
            });
        }

        /* ---------- SweetAlert عند الخطأ ---------- */
        const errEl = document.getElementById('loginError');
        if (errEl && window.Swal) {
            Swal.fire({
                icon: 'error',
                title: 'تعذر تسجيل الدخول',
                text: errEl.dataset.msg,
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#d4af37',
                background: '#fbf6ee',
                color: '#1a0508'
            });
        }
    })();
    </script>

</body>

</html>