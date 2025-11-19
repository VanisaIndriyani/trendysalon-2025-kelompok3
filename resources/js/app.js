import './bootstrap';

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
        nextBtn?.addEventListener('click', next);
        prevBtn?.addEventListener('click', prev);
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
                el?.classList.add('border-pink-500');
            } else {
                el?.classList.remove('border-pink-500');
            }
        });

        if (!valid) {
            alert('Lengkapi semua kolom bertanda * sebelum melanjutkan scan.');
            return;
        }

        // Simpan identitas & preferensi pengguna untuk dianalisis bersama foto
        try {
            sessionStorage.setItem('userName', name?.value || '');
            sessionStorage.setItem('userPhone', phone?.value || '');
            sessionStorage.setItem('prefLength', length?.value || '');
            sessionStorage.setItem('prefType', type?.value || '');
            sessionStorage.setItem('prefCondition', condition?.value || '');
        } catch {}

        const target = submit.getAttribute('data-target') || 'scan/camera';
        const nextUrl = new URL(target, window.location.href).toString();
        window.location.href = nextUrl;
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
            errEl?.classList.add('hidden');
        } catch (err) {
            console.error('Camera error:', err);
            errEl?.classList.remove('hidden');
        }
    };

    await startCamera(currentFacing);

    switchBtn?.addEventListener('click', async () => {
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
        const resultsUrl = new URL('scan/results', window.location.href).toString();
        window.location.href = resultsUrl;
    });
});

// Results page image preview
document.addEventListener('DOMContentLoaded', () => {
    const page = document.getElementById('scanResultPage');
    if (!page) return;

    const dataUrl = sessionStorage.getItem('scanImage');
    const faceShape = sessionStorage.getItem('faceShape');
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

    tryOnScale?.addEventListener('input', applyTransform);
    tryOnOffsetY?.addEventListener('input', applyTransform);
    tryOnOffsetX?.addEventListener('input', applyTransform);
    tryOnClose?.addEventListener('click', () => tryOn?.classList.add('hidden'));

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

            const resultsPromise = new Promise((resolve) => {
                faceMesh.onResults(resolve);
            });
            await faceMesh.send({ image: img });
            const results = await resultsPromise;
            const landmarks = results?.multiFaceLandmarks?.[0];
            if (!landmarks || !Array.isArray(landmarks)) return;

            // Compute bounding box from landmarks (normalized [0..1])
            let minX = 1, minY = 1, maxX = 0, maxY = 0;
            for (const p of landmarks) {
                if (p.x < minX) minX = p.x;
                if (p.y < minY) minY = p.y;
                if (p.x > maxX) maxX = p.x;
                if (p.y > maxY) maxY = p.y;
            }
            const faceW = (maxX - minX) * W;
            const faceH = (maxY - minY) * H;
            const faceCenterX = ((minX + maxX) / 2) * W;

            // Heuristic: hair overlay width ~ 1.6x face width
            const targetRatio = 1.6;
            let autoScale = faceW ? (targetRatio * faceW) / W : 1.0; // to 0.8..1.4
            autoScale = Math.max(0.8, Math.min(1.4, autoScale));

            // Offset Y: align top of hair to near forehead (use minY)
            let autoOffsetY = Math.round((minY * H) * -0.25); // up by 25% of forehead distance
            autoOffsetY = Math.max(-40, Math.min(40, autoOffsetY));

            // Offset X: align to face center, small proportion to avoid jump
            let autoOffsetX = Math.round((faceCenterX - W / 2) * 0.5);
            autoOffsetX = Math.max(-60, Math.min(60, autoOffsetX));

            autoScaleDefault = autoScale;
            autoOffsetDefault = autoOffsetY;
            autoOffsetXDefault = autoOffsetX;
        } catch (err) {
            console.warn('FaceMesh init error:', err);
        }
    };
    initFaceMesh();

    // Tampilkan badge bentuk wajah
    const shapeBadge = document.createElement('div');
    shapeBadge.className = 'mt-4 inline-flex items-center gap-2 rounded-xl bg-pink-100 px-3 py-2 text-xs text-stone-700';
    shapeBadge.innerHTML = `<span class="h-2 w-2 rounded-full bg-pink-400"></span> Bentuk wajah terdeteksi: <strong>${faceShape || 'oval'}</strong>`;
    list.before(shapeBadge);

    const render = (items) => {
        list.innerHTML = '';
        items.forEach((it) => {
            const card = document.createElement('div');
            card.className = 'rounded-xl bg-white shadow px-3 py-3';
            card.innerHTML = `
                <div class="h-36 rounded-lg bg-stone-200 overflow-hidden">
                    <img src="${it.image_url || '/img/model1.png'}" alt="${it.name || 'Model Rambut'}" class="h-36 w-full object-cover" />
                </div>
                <p class="mt-2 text-center text-sm">${it.name || ''}</p>
            `;
            list.appendChild(card);

            // Klik kartu -> coba overlay di wajah pengguna
            card.addEventListener('click', () => {
                if (tryOnOverlay) {
                    // Coba cari overlay PNG transparan berdasarkan nama model
                    const slug = (it.name || '')
                        .toLowerCase()
                        .replace(/[^a-z0-9]+/g, '-')
                        .replace(/^-|-$/g, '');
                    const overlayGuess = slug ? `/img/overlays/${slug}.png` : '';

                    // Fallback ke gambar biasa bila overlay tidak tersedia
                    tryOnOverlay.onerror = () => {
                        tryOnOverlay.src = it.image_url || '/img/model1.png';
                        applyTransform();
                        tryOn?.classList.remove('hidden');
                    };
                    tryOnOverlay.onload = () => {
                        applyTransform();
                        tryOn?.classList.remove('hidden');
                    };
                    tryOnOverlay.src = overlayGuess || (it.image_url || '/img/model1.png');
                    // Reset kontrol default
                    const scaleVal = autoScaleDefault ? String(Math.round(autoScaleDefault * 100)) : '100';
                    const offsetYVal = autoOffsetDefault !== null ? String(autoOffsetDefault) : '0';
                    const offsetXVal = autoOffsetXDefault !== null ? String(autoOffsetXDefault) : '0';
                    if (tryOnScale) tryOnScale.value = scaleVal;
                    if (tryOnOffsetY) tryOnOffsetY.value = offsetYVal;
                    if (tryOnOffsetX) tryOnOffsetX.value = offsetXVal;
                    // applyTransform dipanggil pada onload di atas
                }
            });
        });
    };

    const analyze = async () => {
        try {
            const resp = await fetch('/scan/analyze', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({
                    image: dataUrl,
                    face_shape: faceShape,
                    user_name: userName,
                    user_phone: userPhone,
                    pref: {
                        length: prefLength,
                        type: prefType,
                        condition: prefCondition,
                    },
                }),
            });
            const json = await resp.json();
            loading?.classList.add('hidden');
            if (json && json.ok && json.recommendations) {
                render(json.recommendations);
            } else {
                render([
                    { name: 'Oval Layer with Curtain Bangs', image_url: '/img/model1.png' },
                    { name: 'Butterfly Layer', image_url: '/img/model2.png' },
                    { name: 'Wolf Cut Long Hair', image_url: '/img/model3.png' },
                ]);
            }
        } catch (e) {
            console.error('Analyze error', e);
            loading?.classList.add('hidden');
            render([
                { name: 'Oval Layer with Curtain Bangs', image_url: '/img/model1.png' },
                { name: 'Butterfly Layer', image_url: '/img/model2.png' },
                { name: 'Wolf Cut Long Hair', image_url: '/img/model3.png' },
            ]);
        }
    };

    analyze();
});
