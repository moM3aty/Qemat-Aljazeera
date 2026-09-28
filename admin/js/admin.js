/* ============================================================
 *  admin/js/admin.js   —   المسار:  /admin/js/admin.js
 *  سكربت لوحة التحكم: SweetAlert + Icon Picker + Helpers
 * ============================================================ */

/* ============================================================
 *  قائمة أيقونات Font Awesome الجاهزة
 * ============================================================ */
const FA_ICONS = {
    'خيام وبيوت شعر': [
        'fa-solid fa-campground', 'fa-solid fa-tent', 'fa-solid fa-tent-arrow-down-to-line',
        'fa-solid fa-tent-arrows-down', 'fa-solid fa-house', 'fa-solid fa-house-chimney',
        'fa-solid fa-building', 'fa-solid fa-warehouse'
    ],
    'أثاث ومجالس': [
        'fa-solid fa-couch', 'fa-solid fa-chair', 'fa-solid fa-rug',
        'fa-solid fa-bed', 'fa-solid fa-table', 'fa-solid fa-utensils',
        'fa-solid fa-mug-hot', 'fa-solid fa-cake-candles', 'fa-solid fa-door-open'
    ],
    'إضاءة وتدفئة': [
        'fa-solid fa-lightbulb', 'fa-solid fa-fire', 'fa-solid fa-fire-flame-curved',
        'fa-solid fa-snowflake', 'fa-solid fa-fan', 'fa-solid fa-plug',
        'fa-solid fa-bolt', 'fa-solid fa-sun', 'fa-solid fa-wand-magic-sparkles',
        'fa-solid fa-sparkles'
    ],
    'خدمات وتوصيل': [
        'fa-solid fa-truck-fast', 'fa-solid fa-truck', 'fa-solid fa-wrench',
        'fa-solid fa-screwdriver-wrench', 'fa-solid fa-tools', 'fa-solid fa-box',
        'fa-solid fa-boxes-stacked', 'fa-solid fa-cart-shopping', 'fa-solid fa-handshake'
    ],
    'تواصل ودعم': [
        'fa-solid fa-phone', 'fa-solid fa-headset', 'fa-solid fa-envelope',
        'fa-solid fa-comments', 'fa-solid fa-comment-dots', 'fa-solid fa-mobile-screen',
        'fa-brands fa-whatsapp', 'fa-brands fa-facebook'
    ],
    'مميزات وشارات': [
        'fa-solid fa-star', 'fa-solid fa-heart', 'fa-solid fa-thumbs-up',
        'fa-solid fa-check', 'fa-solid fa-circle-check', 'fa-solid fa-shield',
        'fa-solid fa-shield-halved', 'fa-solid fa-award', 'fa-solid fa-medal',
        'fa-solid fa-trophy', 'fa-solid fa-crown', 'fa-solid fa-gem',
        'fa-solid fa-certificate'
    ],
    'مجموعات وأشخاص': [
        'fa-solid fa-users', 'fa-solid fa-user-group', 'fa-solid fa-user',
        'fa-solid fa-people-group', 'fa-solid fa-users-rectangle', 'fa-solid fa-hand-holding-heart'
    ],
    'عامة': [
        'fa-solid fa-calendar', 'fa-solid fa-clock', 'fa-solid fa-location-dot',
        'fa-solid fa-map-location-dot', 'fa-solid fa-compass', 'fa-solid fa-ruler-combined',
        'fa-solid fa-layer-group', 'fa-solid fa-palette', 'fa-solid fa-brush',
        'fa-solid fa-paint-roller', 'fa-solid fa-gift', 'fa-solid fa-music',
        'fa-solid fa-microphone', 'fa-solid fa-camera', 'fa-solid fa-image',
        'fa-solid fa-images', 'fa-solid fa-video', 'fa-solid fa-list-check'
    ]
};

/* ============================================================
 *  Toast قصير لإشعارات النجاح
 * ============================================================ */
const Toast = Swal.mixin({
    toast: true,
    position: 'top-start',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (t) => {
        t.addEventListener('mouseenter', Swal.stopTimer);
        t.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

/* ============================================================
 *  تشغيل عند تحميل الصفحة
 * ============================================================ */
document.addEventListener('DOMContentLoaded', function () {

    /* ---------- فتح/غلق القائمة الجانبية ---------- */
    const burger = document.getElementById('burger');
    if (burger) {
        burger.addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('open');
        });
    }

    /* ---------- تأكيد الحذف عبر SweetAlert ---------- */
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (ev) {
            if (form.dataset.confirmed === '1') return;
            ev.preventDefault();

            Swal.fire({
                icon: 'warning',
                title: 'هل أنت متأكد؟',
                text: form.dataset.confirm,
                showCancelButton: true,
                confirmButtonText: '<i class="fa-solid fa-trash"></i> نعم، احذف',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#c0392b',
                cancelButtonColor: '#6b5f5a',
                reverseButtons: true,
                focusCancel: true
            }).then(function (res) {
                if (res.isConfirmed) {
                    form.dataset.confirmed = '1';
                    form.submit();
                }
            });
        });
    });

    /* ---------- تأكيد تسجيل الخروج ---------- */
    document.querySelectorAll('a[data-logout]').forEach(function (link) {
        link.addEventListener('click', function (ev) {
            ev.preventDefault();
            Swal.fire({
                icon: 'question',
                title: 'تسجيل الخروج',
                text: 'هل تريد الخروج من لوحة التحكم؟',
                showCancelButton: true,
                confirmButtonText: 'نعم، اخرج',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#d4af37',
                cancelButtonColor: '#6b5f5a',
                reverseButtons: true
            }).then(function (res) {
                if (res.isConfirmed) window.location.href = link.href;
            });
        });
    });

    /* ---------- معاينة الصور قبل الرفع ---------- */
    document.querySelectorAll('input[type=file][data-preview]').forEach(function (inp) {
        inp.addEventListener('change', function () {
            const box = document.querySelector(inp.dataset.preview);
            if (!box) return;
            box.innerHTML = '';
            Array.prototype.forEach.call(inp.files, function (file) {
                if (!file.type.startsWith('image/')) return;
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                box.appendChild(img);
            });
        });
    });

    /* ---------- Icon Picker ---------- */
    initIconPicker();

    /* ---------- Quill Rich Text ---------- */
    initQuill();
});

/* ============================================================
 *  Icon Picker
 * ============================================================ */
function initIconPicker() {
    const pickers = document.querySelectorAll('[data-icon-picker]');
    if (!pickers.length) return;

    /* بناء الـ Modal مرة واحدة */
    const modal = document.createElement('div');
    modal.className = 'icon-modal';
    modal.id = 'iconModal';
    modal.innerHTML = `
        <div class="icon-modal-content">
            <div class="icon-modal-header">
                <h3><i class="fa-solid fa-icons"></i> اختر أيقونة</h3>
                <button type="button" class="icon-modal-close" aria-label="إغلاق">&times;</button>
            </div>
            <div class="icon-modal-search">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="ابحث عن أيقونة (مثال: phone, tent, fire ...)">
            </div>
            <div class="icon-modal-body" id="iconModalBody"></div>
        </div>`;
    document.body.appendChild(modal);

    const body      = modal.querySelector('#iconModalBody');
    const searchInp = modal.querySelector('.icon-modal-search input');

    /* رسم الأيقونات */
    function render(filter = '') {
        body.innerHTML = '';
        const f = filter.trim().toLowerCase();
        let found = 0;

        Object.keys(FA_ICONS).forEach(function (group) {
            const icons = FA_ICONS[group].filter(ic => !f || ic.toLowerCase().includes(f));
            if (!icons.length) return;

            const title = document.createElement('div');
            title.className = 'icon-group-title';
            title.textContent = group;
            body.appendChild(title);

            const grid = document.createElement('div');
            grid.className = 'icon-grid';

            icons.forEach(function (cls) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'icon-cell';
                btn.dataset.icon = cls;
                btn.title = cls;
                btn.innerHTML = `<i class="${cls}"></i>`;
                grid.appendChild(btn);
                found++;
            });
            body.appendChild(grid);
        });

        if (!found) {
            body.innerHTML = '<p class="icon-empty">لا توجد أيقونات مطابقة للبحث.</p>';
        }
    }
    render();

    searchInp.addEventListener('input', function () {
        render(this.value);
    });

    /* فتح الـ Modal */
    let currentPicker = null;
    pickers.forEach(function (picker) {
        picker.querySelector('.icon-picker-open')?.addEventListener('click', function () {
            currentPicker = picker;
            modal.classList.add('open');
            searchInp.value = '';
            render('');
            setTimeout(() => searchInp.focus(), 80);
        });
    });

    /* اختيار أيقونة */
    body.addEventListener('click', function (ev) {
        const cell = ev.target.closest('.icon-cell');
        if (!cell || !currentPicker) return;

        const cls = cell.dataset.icon;
        const inp = currentPicker.querySelector('.icon-input');
        const prev = currentPicker.querySelector('.icon-picker-preview i');
        if (inp) inp.value = cls;
        if (prev) prev.className = cls;

        modal.classList.remove('open');
        Toast.fire({ icon: 'success', title: 'تم اختيار الأيقونة' });
    });

    /* إغلاق */
    modal.querySelector('.icon-modal-close').addEventListener('click', () => modal.classList.remove('open'));
    modal.addEventListener('click', e => { if (e.target === modal) modal.classList.remove('open'); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') modal.classList.remove('open'); });
}

/* ============================================================
 *  Quill Rich Text Editor
 *  يُستخدم بكتابة: <div id="editor"></div> + <textarea name="content" hidden>
 * ============================================================ */
function initQuill() {
    const editorEl = document.querySelector('#editor');
    if (!editorEl) {
        console.warn('[Quill] #editor not found');
        return;
    }

    const textarea = document.querySelector('#contentInput');
    if (!textarea) {
        console.warn('[Quill] #contentInput not found');
        return;
    }

    if (typeof Quill === 'undefined') {
        console.error('[Quill] مكتبة Quill لم يتم تحميلها — تأكد من الـ CDN');
        return;
    }

    const toolbarOptions = [
        [{ 'header': [1, 2, 3, 4, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'color': [] }, { 'background': [] }],
        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
        [{ 'align': [] }],
        ['blockquote', 'code-block'],
        ['link', 'image'],
        ['clean']
    ];

    let quill;
    try {
        quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: textarea.dataset.placeholder || 'اكتب محتوى المقال هنا...',
            modules: { toolbar: toolbarOptions }
        });
    } catch (e) {
        console.error('[Quill] فشل التهيئة:', e);
        return;
    }

    /* اتجاه RTL */
    quill.root.setAttribute('dir', 'rtl');
    quill.root.style.textAlign = 'right';
    const toolbar = document.querySelector('.ql-toolbar');
    if (toolbar) toolbar.setAttribute('dir', 'ltr');

    /* تحميل المحتوى الأولي */
    const initial = textarea.value || '';
    if (initial.trim() && initial.trim() !== '<p><br></p>') {
        try {
            quill.clipboard.dangerouslyPasteHTML(initial);
        } catch (e) {
            console.warn('[Quill] فشل تحميل المحتوى:', e);
        }
    }

    /* ✅ المزامنة الفورية — تُحدّث الـ textarea مع كل تغيير */
    function syncToTextarea() {
        const html = quill.root.innerHTML;
        textarea.value = (html === '<p><br></p>') ? '' : html;
    }

    /* مزامنة عند أي تغيير في المحرر */
    quill.on('text-change', syncToTextarea);

    /* مزامنة أولية */
    syncToTextarea();

    /* مزامنة احتياطية قبل الإرسال (Capture phase) */
    const form = textarea.closest('form');
    if (form) {
        form.addEventListener('submit', function () {
            syncToTextarea();
            console.log('[Quill] تمت المزامنة قبل الإرسال. الطول:', textarea.value.length);
        }, true); /* ← capture: true */
    }

    /* رفع صورة داخل المحرر (اختياري) */
    try {
        quill.getModule('toolbar').addHandler('image', function () {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.click();
            input.onchange = function () {
                const file = input.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = function () {
                    const range = quill.getSelection(true);
                    quill.insertEmbed(range.index, 'image', reader.result);
                };
                reader.readAsDataURL(file);
            };
        });
    } catch (e) {
        console.warn('[Quill] custom image handler skipped:', e);
    }
}