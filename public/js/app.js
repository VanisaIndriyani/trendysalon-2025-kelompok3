// Simple slider for the hero section
document.addEventListener('DOMContentLoaded', () => {
    const slider = document.getElementById('heroSlider');
    if (!slider) return;

    const slides = Array.from(slider.querySelectorAll('.slide'));
    const prevBtn = slider.querySelector('.prev');
    const nextBtn = slider.querySelector('.next');
    let index = 0;

    const show = (i) => {
        slides.forEach((s, idx) => {
            if (idx === i) {
                s.classList.remove('hidden');
            } else {
                s.classList.add('hidden');
            }
        });
    };

    const next = () => {
        index = (index + 1) % slides.length;
        show(index);
    };

    const prev = () => {
        index = (index - 1 + slides.length) % slides.length;
        show(index);
    };

    if (slides.length > 1) {
        if (nextBtn) nextBtn.addEventListener('click', next);
        if (prevBtn) prevBtn.addEventListener('click', prev);
        // Auto-play every 5 seconds
        setInterval(next, 5000);
    }

    // Initial render (single or multiple)
    show(index);
});

// Mobile menu toggle
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('menuToggle');
    const panel = document.getElementById('mobileMenu');
    if (!toggle || !panel) return;

    toggle.addEventListener('click', () => {
        panel.classList.toggle('hidden');
    });

    panel.querySelectorAll('a').forEach((a) => {
        a.addEventListener('click', () => panel.classList.add('hidden'));
    });
});

// Scan form simple validation
document.addEventListener('DOMContentLoaded', () => {
    const submit = document.getElementById('scanSubmit');
    const form = document.getElementById('scanForm');
    if (!submit || !form) return;

    submit.addEventListener('click', () => {
        const name = document.getElementById('nameInput');
        const phone = document.getElementById('phoneInput');
        const length = document.getElementById('lengthSelect');
        const type = document.getElementById('typeSelect');
        const condition = document.getElementById('conditionSelect');

        const fields = [name, phone, length, type, condition];
        let valid = true;
        fields.forEach((el) => {
            if (!el || !el.value) {
                valid = false;
                if (el) el.classList.add('border-pink-500');
            } else {
                if (el) el.classList.remove('border-pink-500');
            }
        });

        if (!valid) {
            alert('Lengkapi semua kolom bertanda * sebelum melanjutkan scan.');
            return;
        }

        // Simpan identitas & preferensi pengguna untuk dianalisis bersama foto
        try {
            sessionStorage.setItem('userName', (name && name.value) || '');
            sessionStorage.setItem('userPhone', (phone && phone.value) || '');
            sessionStorage.setItem('prefLength', (length && length.value) || '');
            sessionStorage.setItem('prefType', (type && type.value) || '');
            sessionStorage.setItem('prefCondition', (condition && condition.value) || '');
        } catch {}

        // Gunakan path relatif agar bekerja di subdirektori (mis. /trendysalon/public)
        // Prefer absolute URL injected by Blade; fallback to relative './camera' from /scan
        const target = submit.getAttribute('data-target') || (window.__SCAN_ROUTES__?.camera || './camera');
        window.location.href = target;
    });
});

// Camera activation and capture -> results
document.addEventListener('DOMContentLoaded', async () => {
    const page = document.getElementById('scanCameraPage');
    if (!page) return;

    const video = document.getElementById('cameraVideo');
    const canvas = document.getElementById('cameraCanvas');
    const placeholder = document.getElementById('cameraPlaceholder');
    const captureBtn = document.getElementById('captureBtn');
    const switchBtn = document.getElementById('switchCameraBtn');
    const errEl = document.getElementById('cameraError');
    let currentFacing = 'user';
    let activeStream = null;

    const startCamera = async (facing = 'user') => {
        try {
            if (activeStream) {
                activeStream.getTracks().forEach(t => t.stop());
            }
            const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: facing } });
            activeStream = stream;
            video.srcObject = stream;
            video.classList.remove('hidden');
            placeholder.classList.add('hidden');
            if (errEl) errEl.classList.add('hidden');
        } catch (err) {
            console.error('Camera error:', err);
            if (errEl) errEl.classList.remove('hidden');
        }
    };

    await startCamera(currentFacing);

    if (switchBtn) switchBtn.addEventListener('click', async () => {
        currentFacing = currentFacing === 'user' ? 'environment' : 'user';
        await startCamera(currentFacing);
    });

    captureBtn?.addEventListener('click', () => {
        if (!video) return;
        const width = video.videoWidth || 640;
        const height = video.videoHeight || 480;
        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, width, height);
        const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
        // Deteksi bentuk wajah sederhana berdasarkan rasio bingkai
        const ratio = width / height;
        let faceShape = 'oval';
        if (ratio >= 0.95 && ratio <= 1.05) faceShape = 'bulat';
        else if (ratio < 0.95) faceShape = 'lonjong';
        else if (ratio > 1.05) faceShape = 'oval';

        // Simpan hasil capture & bentuk wajah
        try {
            sessionStorage.setItem('scanImage', dataUrl);
            sessionStorage.setItem('faceShape', faceShape);
        } catch {}
        // Bangun URL hasil berbasis lokasi saat ini agar base path ikut terbawa
        // Prefer absolute URL injected by Blade; fallback to relative './results' from /scan/camera
        const resultsUrl = window.__SCAN_ROUTES__?.results || './results';
        window.location.href = resultsUrl;
    });
});

// Results page image preview
document.addEventListener('DOMContentLoaded', () => {
    const page = document.getElementById('scanResultPage');
    if (!page) return;

    const dataUrl = sessionStorage.getItem('scanImage');
    let faceShape = sessionStorage.getItem('faceShape') || 'oval';
    const userName = sessionStorage.getItem('userName');
    const userPhone = sessionStorage.getItem('userPhone');
    const prefLength = sessionStorage.getItem('prefLength');
    const prefType = sessionStorage.getItem('prefType');
    const prefCondition = sessionStorage.getItem('prefCondition');
    if (dataUrl) {
        const wrap = document.getElementById('capturePreview');
        const img = document.getElementById('captureImage');
        if (wrap && img) {
            img.src = dataUrl;
            wrap.classList.remove('hidden');
        }
    }

    // Fetch AI recommendations
    const tokenEl = document.querySelector('meta[name="csrf-token"]');
    const csrf = tokenEl ? tokenEl.content : '';
    const loading = document.getElementById('loadingAnalysis');
    const list = document.getElementById('recommendations');
    if (!list) return;

    // Try-on elements
    const tryOn = document.getElementById('tryOnControls');
    const tryOnBase = document.getElementById('tryOnBase');
    const tryOnOverlay = document.getElementById('tryOnOverlay');
    const tryOnScale = document.getElementById('tryOnScale');
    const tryOnOffsetY = document.getElementById('tryOnOffsetY');
    const tryOnOffsetX = document.getElementById('tryOnOffsetX');
    const tryOnClose = document.getElementById('tryOnClose');
    if (tryOnBase && dataUrl) {
        tryOnBase.src = dataUrl;
    }

    // Default transform computed from FaceMesh (auto)
    let autoScaleDefault = null; // number 0.8..1.4
    let autoOffsetDefault = null; // px -40..40 (Y)
    let autoOffsetXDefault = null; // px -60..60 (X)

    const applyTransform = () => {
        const scale = Number(tryOnScale?.value || 100) / 100;
        const offsetY = Number(tryOnOffsetY?.value || 0);
        const offsetX = Number(tryOnOffsetX?.value || 0);
        if (tryOnOverlay) {
            tryOnOverlay.style.transform = `translateX(calc(-50% + ${offsetX}px)) translateY(${offsetY}px) scale(${scale})`;
        }
    };

    if (tryOnScale) tryOnScale.addEventListener('input', applyTransform);
    if (tryOnOffsetY) tryOnOffsetY.addEventListener('input', applyTransform);
    if (tryOnOffsetX) tryOnOffsetX.addEventListener('input', applyTransform);
    if (tryOnClose) tryOnClose.addEventListener('click', () => { if (tryOn) tryOn.classList.add('hidden'); });

    // Initialize FaceMesh on captured image to compute face bounding box
    const initFaceMesh = async () => {
        try {
            // Verify library is available
            const FaceMeshLib = window.FaceMesh;
            if (!FaceMeshLib || !FaceMeshLib.FaceMesh || !dataUrl) return;

            const stage = document.getElementById('tryOnStage');
            const W = stage?.clientWidth || 288; // 72*4px
            const H = stage?.clientHeight || 288;

            // Prepare image element
            const img = new Image();
            img.src = dataUrl;
            await img.decode();

            // Setup FaceMesh
            const faceMesh = new FaceMeshLib.FaceMesh({
                locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${file}`,
            });
            faceMesh.setOptions({
                selfieMode: true,
                maxNumFaces: 1,
                refineLandmarks: true,
                minDetectionConfidence: 0.5,
                minTrackingConfidence: 0.5,
            });

            const results = await new Promise((resolve) => {
                faceMesh.onResults(resolve);
                faceMesh.send({ image: img });
            });

            const landmarks = results.multiFaceLandmarks?.[0];
            if (!landmarks) return;

            // Compute bounding box roughly from landmarks
            let minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
            landmarks.forEach((pt) => {
                minX = Math.min(minX, pt.x);
                minY = Math.min(minY, pt.y);
                maxX = Math.max(maxX, pt.x);
                maxY = Math.max(maxY, pt.y);
            });

            const bboxW = (maxX - minX) * W;
            const bboxH = (maxY - minY) * H;
            // Set plausible default overlay scale and offsets
            autoScaleDefault = Math.min(1.4, Math.max(0.8, 1.0 + (bboxW - W * 0.5) / (W * 0.8)));
            autoOffsetDefault = Math.min(40, Math.max(-40, (minY * H - H * 0.15)));
            autoOffsetXDefault = Math.min(60, Math.max(-60, ((minX + maxX) / 2 * W - W / 2)));

            if (tryOnOverlay) {
                // initialize starting transform
                tryOnOverlay.style.transform = `translateX(calc(-50% + ${autoOffsetXDefault}px)) translateY(${autoOffsetDefault}px) scale(${autoScaleDefault})`;
            }
            if (tryOn) tryOn.classList.remove('hidden');
        } catch (e) {
            console.warn('FaceMesh init error:', e);
        }
    };

    initFaceMesh();

    // Load recommendations
    // faceShape sudah dideklarasikan di atas, gunakan nilai default 'oval' jika null
    const loadRecs = async () => {
        try {
            const apiUrl = (window.__SCAN_ROUTES__ && window.__SCAN_ROUTES__.apiModels) ? window.__SCAN_ROUTES__.apiModels : '../api/recommendations/hair-models';
            const res = await fetch(`${apiUrl}?face_shape=${encodeURIComponent(faceShape)}`);
            const data = await res.json();
            loading?.classList.add('hidden');
            if (!Array.isArray(data) || data.length === 0) {
                list.innerHTML = '<p class="text-xs text-stone-600">Belum ada rekomendasi untuk bentuk wajah ini.</p>';
                return;
            }

            list.innerHTML = data.map((m) => `
                <button class="group rounded-xl border border-stone-200 bg-white p-3 text-left shadow hover:shadow-md" data-overlay="${m.illustration_url || ''}">
                    <div class="flex items-center gap-3">
                        <img src="${m.illustration_url || ''}" alt="${m.name || 'Model'}" class="h-14 w-14 rounded-lg object-cover">
                        <div>
                            <p class="text-sm font-semibold">${m.name || 'Model'}</p>
                            <p class="text-xs text-stone-600">${m.hair_length || ''} • ${m.hair_type || ''}</p>
                        </div>
                    </div>
                </button>
            `).join('');

            // Bind try-on overlay
            list.querySelectorAll('button[data-overlay]').forEach((btn) => {
                btn.addEventListener('click', () => {
                    const url = btn.getAttribute('data-overlay');
                    if (tryOnOverlay && url) {
                        tryOnOverlay.src = url;
                        if (tryOn) tryOn.classList.remove('hidden');
                        applyTransform();
                    }
                });
            });
        } catch (e) {
            loading?.classList.add('hidden');
            list.innerHTML = '<p class="text-xs text-red-600">Gagal memuat rekomendasi.</p>';
        }
    };

    loadRecs();
});