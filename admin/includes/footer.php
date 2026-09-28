<?php
/* ============================================================
 *  admin/includes/footer.php   —   المسار:  /admin/includes/footer.php
 * ============================================================ */
$flash = get_flash();
?>
        </div><!-- /.content -->

        <footer class="admin-footer">
            <?= e(setting('copyright')) ?> — لوحة التحكم
        </footer>
    </main>
</div>

<!-- SweetAlert2 + Quill + Admin Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script src="js/admin.js"></script>

<?php if ($flash): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        icon: <?= $flash['type'] === 'error' ? "'error'" : "'success'" ?>,
        title: <?= $flash['type'] === 'error' ? "'حدث خطأ'" : "'تم بنجاح'" ?>,
        html: <?= json_encode($flash['msg']) ?>,
        confirmButtonText: 'حسناً',
        confirmButtonColor: '#d4af37',
        timer: <?= $flash['type'] === 'error' ? 5000 : 3500 ?>,
        timerProgressBar: true
    });
});
</script>
<?php endif; ?>
</body>
</html>