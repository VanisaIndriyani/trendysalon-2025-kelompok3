import './bootstrap';

// Debug flag - set to false in production
const DEBUG = window.DEBUG !== false;

// Simple slider for the hero section
document.addEventListener('DOMContentLoaded', () => {
    const slider = document.getElementById('heroSlider');
    if (!slider) return;

    const slides = Array.from(slider.querySelectorAll('.slide'));
    const prevBtn = slider.querySelector('.prev');
    const nextBtn = slider.querySelector('.next');
    let index = 0;
    let sliderIntervalId = null;

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
        sliderIntervalId = setInterval(next, 5000);
    }

    // Initial render (single or multiple)
    show(index);

    // Cleanup interval on page unload
    window.addEventListener('pagehide', () => {
        if (sliderIntervalId) {
            clearInterval(sliderIntervalId);
            sliderIntervalId = null;
        }
    });
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
        } catch (err) {
            if (DEBUG) console.warn('sessionStorage setItem failed:', err);
        }

        const target = submit.getAttribute('data-target') || (window.__SCAN_ROUTES__?.camera || './camera');
        window.location.href = target;
    });
});

// Camera activation and capture -> results
document.addEventListener('DOMContentLoaded', async () => {
    const page = document.getElementById('scanCameraPage');
    if (!page) {
        // ✅ JANGAN LOG - INI NORMAL JIKA BUKAN CAMERA PAGE
        return;
    }

    console.log('📷 Camera page script loaded!');

    const video = document.getElementById('cameraVideo');
    const canvas = document.getElementById('cameraCanvas');
    const placeholder = document.getElementById('cameraPlaceholder');
    const captureBtn = document.getElementById('captureBtn');
    const switchBtn = document.getElementById('switchCameraBtn');
    const errEl = document.getElementById('cameraError');

    // ✅ VALIDASI TOMBOL - PASTIKAN TOMBOL ADA SEBELUM VALIDASI ELEMEN LAIN
    if (!captureBtn) {
        console.error('❌❌❌ Capture button not found! Cannot proceed.');
        if (errEl) {
            errEl.classList.remove('hidden');
            errEl.textContent = 'Tombol capture tidak ditemukan.';
        }
        return; // Exit jika tombol capture tidak ada (wajib)
    }

    console.log('✅ Capture button found:', {
        id: captureBtn.id,
        tagName: captureBtn.tagName,
        disabled: captureBtn.disabled,
        hasDisabledAttr: captureBtn.hasAttribute('disabled')
    });

    // Validasi elemen penting
    if (!(video instanceof HTMLVideoElement) || !(canvas instanceof HTMLCanvasElement)) {
        if (DEBUG) console.error('Camera elements missing or wrong type. Aborting camera init.');
        if (errEl) {
            errEl.classList.remove('hidden');
            errEl.textContent = 'Elemen kamera tidak ditemukan.';
        }
        return;
    }

    // ✅ PASTIKAN TOMBOL ADALAH HTMLElement
    if (!(captureBtn instanceof HTMLElement)) {
        console.error('❌ Capture button is not an HTMLElement!');
        return;
    }
    
    // ✅ PASTIKAN TOMBOL BISA DIKLIK SAAT PERTAMA KALI
    captureBtn.disabled = false;
    captureBtn.style.pointerEvents = 'auto';
    captureBtn.style.cursor = 'pointer';
    captureBtn.style.opacity = '1';
    captureBtn.style.zIndex = '50';
    captureBtn.style.position = 'relative';
    
    // ✅ FORCE ENABLE - JANGAN DISABLE SAAT PERTAMA KALI
    // Real-time face detection akan enable/disable nanti
    console.log('✅ Capture button initialized:', {
        disabled: captureBtn.disabled,
        pointerEvents: captureBtn.style.pointerEvents,
        zIndex: captureBtn.style.zIndex
    });
    
    // switchBtn adalah optional - tidak perlu warning jika tidak ada

    // Guard untuk getUserMedia availability
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        if (DEBUG) console.error('getUserMedia not supported by this browser.');
        if (errEl) {
            errEl.classList.remove('hidden');
            errEl.textContent = 'Kamera tidak didukung oleh browser ini.';
        }
        return;
    }

    let currentFacing = 'user';
    let activeStream = null;
    let faceDetectionInterval = null;
    const multipleFacesWarning = document.getElementById('multipleFacesWarning');
    const faceDetectionModal = document.getElementById('faceDetectionModal');
    const faceDetectionModalClose = document.getElementById('faceDetectionModalClose');
    const faceDetectionModalOk = document.getElementById('faceDetectionModalOk');
    
    // ✅ MODAL MULTIPLE FACES
    const multipleFacesModal = document.getElementById('multipleFacesModal');
    const multipleFacesModalClose = document.getElementById('multipleFacesModalClose');
    const multipleFacesModalOk = document.getElementById('multipleFacesModalOk');
    const multipleFacesModalRetry = document.getElementById('multipleFacesModalRetry');
    const multipleFacesModalTitle = document.getElementById('multipleFacesModalTitle');
    const multipleFacesModalMessage = document.getElementById('multipleFacesModalMessage');
    
    // ✅ FUNGSI UNTUK TAMPILKAN MODAL PINK (FACE DETECTION)
    const showFaceDetectionModal = () => {
        if (faceDetectionModal) {
            faceDetectionModal.classList.remove('hidden');
            faceDetectionModal.classList.add('flex');
            // Prevent body scroll when modal is open
            document.body.style.overflow = 'hidden';
        }
    };
    
    // ✅ FUNGSI UNTUK TUTUP MODAL (FACE DETECTION)
    const hideFaceDetectionModal = () => {
        if (faceDetectionModal) {
            faceDetectionModal.classList.add('hidden');
            faceDetectionModal.classList.remove('flex');
            // Restore body scroll
            document.body.style.overflow = '';
        }
    };
    
    // ✅ FUNGSI UNTUK TAMPILKAN MODAL PINK (MULTIPLE FACES)
    const showMultipleFacesModal = (title, message) => {
        if (multipleFacesModal) {
            // Update title dan message jika ada
            if (title && multipleFacesModalTitle) {
                multipleFacesModalTitle.textContent = title;
            }
            if (message && multipleFacesModalMessage) {
                multipleFacesModalMessage.textContent = message;
            }
            
            multipleFacesModal.classList.remove('hidden');
            multipleFacesModal.classList.add('flex');
            // Prevent body scroll when modal is open
            document.body.style.overflow = 'hidden';
        }
    };
    
    // ✅ FUNGSI UNTUK TUTUP MODAL (MULTIPLE FACES)
    const hideMultipleFacesModal = () => {
        if (multipleFacesModal) {
            multipleFacesModal.classList.add('hidden');
            multipleFacesModal.classList.remove('flex');
            // Restore body scroll
            document.body.style.overflow = '';
        }
    };
    
    // ✅ EVENT LISTENER UNTUK TUTUP MODAL (FACE DETECTION)
    if (faceDetectionModalClose) {
        faceDetectionModalClose.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            hideFaceDetectionModal();
        });
    }
    
    if (faceDetectionModalOk) {
        faceDetectionModalOk.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            hideFaceDetectionModal();
        });
    }
    
    // ✅ TUTUP MODAL JIKA KLIK DI LUAR MODAL (FACE DETECTION)
    if (faceDetectionModal) {
        faceDetectionModal.addEventListener('click', (e) => {
            if (e.target === faceDetectionModal) {
                hideFaceDetectionModal();
            }
        });
    }
    
    // ✅ EVENT LISTENER UNTUK TUTUP MODAL (MULTIPLE FACES)
    if (multipleFacesModalClose) {
        multipleFacesModalClose.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            hideMultipleFacesModal();
        });
    }
    
    if (multipleFacesModalOk) {
        multipleFacesModalOk.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            hideMultipleFacesModal();
        });
    }
    
    if (multipleFacesModalRetry) {
        multipleFacesModalRetry.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            hideMultipleFacesModal();
        });
    }
    
    // ✅ TUTUP MODAL JIKA KLIK DI LUAR MODAL (MULTIPLE FACES)
    if (multipleFacesModal) {
        multipleFacesModal.addEventListener('click', (e) => {
            if (e.target === multipleFacesModal) {
                hideMultipleFacesModal();
            }
        });
    }

    // Real-time face detection monitoring (sebelum capture)
    const startFaceDetectionMonitoring = () => {
        // Hentikan monitoring sebelumnya jika ada
        if (faceDetectionInterval) {
            clearInterval(faceDetectionInterval);
        }
        
        // Cek apakah Face Detection API tersedia
        if (!window.FaceDetector) {
            if (DEBUG) console.warn('Face Detection API not available for real-time monitoring');
            // ✅ TAMPILKAN MODAL PINK JIKA FACE DETECTION API TIDAK TERSEDIA
            if (typeof showFaceDetectionModal === 'function') {
                showFaceDetectionModal();
            }
            return;
        }
        
        const faceDetector = new FaceDetector({ fastMode: true, maxDetectedFaces: 5 });
        
        // Monitor setiap 1 detik
        // ✅ JANGAN DISABLE TOMBOL TERLALU CEPAT - Beri delay 5 detik pertama
        let monitoringStartTime = Date.now();
        const INITIAL_DELAY = 5000; // 5 detik pertama, tombol tetap bisa diklik
        
        faceDetectionInterval = setInterval(async () => {
            if (!video || video.readyState < 2) return; // Video belum ready
            
            // ✅ JANGAN DISABLE TOMBOL SELAMA 3 DETIK PERTAMA
            const timeSinceStart = Date.now() - monitoringStartTime;
            if (timeSinceStart < INITIAL_DELAY) {
                // Biarkan tombol tetap bisa diklik
                if (captureBtn) {
                    captureBtn.disabled = false;
                    captureBtn.style.pointerEvents = 'auto';
                    captureBtn.style.opacity = '1';
                    captureBtn.style.cursor = 'pointer';
                }
                return; // Skip face detection untuk 3 detik pertama
            }
            
            try {
                // Draw current frame ke canvas untuk deteksi
                const tempCanvas = document.createElement('canvas');
                tempCanvas.width = video.videoWidth || 640;
                tempCanvas.height = video.videoHeight || 480;
                const tempCtx = tempCanvas.getContext('2d');
                if (!tempCtx) return;
                
                tempCtx.drawImage(video, 0, 0, tempCanvas.width, tempCanvas.height);
                
                // Deteksi wajah
                const faces = await faceDetector.detect(tempCanvas);
                const faceCount = faces.length;
                
                // ✅ UPDATE WARNING BERDASARKAN JUMLAH WAJAH - TAPI TOMBOL SELALU BISA DIKLIK
                if (faceCount > 1) {
                    // Lebih dari 1 wajah - tampilkan warning
                    if (multipleFacesWarning) {
                        multipleFacesWarning.classList.remove('hidden');
                        const warningDiv = multipleFacesWarning.querySelector('.flex-1');
                        if (warningDiv) {
                            const titleP = warningDiv.querySelector('p:first-child');
                            const descP = warningDiv.querySelector('p:last-child');
                            if (titleP) titleP.textContent = '❌ GAGAL: Terdeteksi Lebih dari 1 Wajah';
                            if (descP) descP.textContent = `AI tidak dapat menganalisis jika ada lebih dari 1 orang dalam foto. Terdeteksi ${faceCount} wajah. Pastikan hanya 1 wajah yang terlihat di kamera.`;
                        }
                    }
                    // ✅ DISABLE TOMBOL JIKA LEBIH DARI 1 WAJAH
                    if (captureBtn) {
                        captureBtn.disabled = true;
                        captureBtn.style.pointerEvents = 'none';
                        captureBtn.style.opacity = '0.5';
                    }
                } else if (faceCount === 0) {
                    // Tidak ada wajah - tampilkan warning
                    if (multipleFacesWarning) {
                        multipleFacesWarning.classList.remove('hidden');
                        const warningDiv = multipleFacesWarning.querySelector('.flex-1');
                        if (warningDiv) {
                            const titleP = warningDiv.querySelector('p:first-child');
                            const descP = warningDiv.querySelector('p:last-child');
                            if (titleP) titleP.textContent = '⚠️ Tidak Ada Wajah Terdeteksi';
                            if (descP) descP.textContent = 'Pastikan wajah Anda terlihat jelas di kamera dan berada di tengah frame.';
                        }
                    }
                    // ✅ TIDAK DISABLE TOMBOL - BIARKAN USER KLIK (VALIDASI DI CAPTURE HANDLER)
                } else {
                    // Tepat 1 wajah - sembunyikan warning
                    if (multipleFacesWarning) {
                        multipleFacesWarning.classList.add('hidden');
                    }
                }
                
                // ✅ PASTIKAN TOMBOL SELALU ENABLE - TIDAK PERNAH DISABLE
                if (captureBtn) {
                    captureBtn.disabled = false;
                    captureBtn.style.opacity = '1';
                    captureBtn.style.cursor = 'pointer';
                    captureBtn.style.pointerEvents = 'auto';
                    captureBtn.style.zIndex = '999';
                }
            } catch (err) {
                if (DEBUG) console.warn('Real-time face detection error:', err);
                // Jika error, enable button (fallback)
                if (captureBtn) {
                    captureBtn.disabled = false;
                    captureBtn.style.opacity = '1';
                    captureBtn.style.cursor = 'pointer';
                    captureBtn.style.pointerEvents = 'auto'; // ✅ FORCE ENABLE
                }
            }
        }, 1000); // Check setiap 1 detik
    };
    
    const stopFaceDetectionMonitoring = () => {
        if (faceDetectionInterval) {
            clearInterval(faceDetectionInterval);
            faceDetectionInterval = null;
        }
    };
    
    // ✅ FUNGSI UNTUK UPDATE ORIENTASI OVERLAY GUIDE
    const updateFaceGuideOrientation = () => {
        const faceGuide = document.getElementById('faceGuideOverlay');
        if (!faceGuide) return;
        
        // Deteksi orientasi perangkat
        let rotation = 0;
        
        // Cek menggunakan Screen Orientation API (modern browsers)
        if (screen.orientation) {
            const angle = screen.orientation.angle;
            // Normalize angle (0, 90, 180, 270)
            rotation = angle;
        } 
        // Fallback untuk browser lama
        else if (window.orientation !== undefined) {
            rotation = window.orientation;
        }
        
        // Normalize rotation ke 0, 90, 180, atau 270
        rotation = Math.round(rotation / 90) * 90;
        
        // Apply rotation ke overlay guide
        const guideContainer = faceGuide.querySelector('div:first-child');
        if (guideContainer) {
            // Reset transform dulu
            guideContainer.style.transform = '';
            
            // Apply rotation berdasarkan orientasi
            // 0° = portrait normal (tidak perlu rotate)
            // 90° = landscape ke kanan (rotate -90°)
            // 180° = portrait terbalik (rotate 180°)
            // 270° = landscape ke kiri (rotate 90°)
            
            if (rotation === 90) {
                // Landscape ke kanan - rotate counter-clockwise
                guideContainer.style.transform = 'translate(-50%, -50%) rotate(-90deg)';
            } else if (rotation === 180) {
                // Portrait terbalik - rotate 180°
                guideContainer.style.transform = 'translate(-50%, -50%) rotate(180deg)';
            } else if (rotation === 270 || rotation === -90) {
                // Landscape ke kiri - rotate clockwise
                guideContainer.style.transform = 'translate(-50%, -50%) rotate(90deg)';
            } else {
                // Portrait normal (0°) - tidak perlu rotate
                guideContainer.style.transform = 'translate(-50%, -50%)';
            }
            
            console.log('🔄 Face guide orientation updated:', {
                rotation,
                transform: guideContainer.style.transform
            });
        }
    };

    const startCamera = async (facing = 'user') => {
        try {
            if (activeStream) {
                activeStream.getTracks().forEach(t => t.stop());
                activeStream = null;
            }
            const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: facing } });
            activeStream = stream;
            video.srcObject = stream;
            
            // NONAKTIFKAN MIRROR - tidak flip horizontal
            video.style.setProperty('transform', 'scaleX(1)', 'important'); // Normal, tidak mirror
            
            await video.play().catch(() => {
                // Ignore autoplay block
            });
            video.classList.remove('hidden');
            placeholder?.classList.add('hidden');
            
            // Tampilkan face guide overlay
            const faceGuide = document.getElementById('faceGuideOverlay');
            if (faceGuide) {
                faceGuide.classList.remove('hidden');
                // ✅ UPDATE ORIENTASI OVERLAY GUIDE
                updateFaceGuideOrientation();
                
                // ✅ LISTEN UNTUK PERUBAHAN ORIENTASI
                // Hapus listener lama jika ada
                if (window._orientationChangeHandler) {
                    window.removeEventListener('orientationchange', window._orientationChangeHandler);
                    window.removeEventListener('resize', window._orientationChangeHandler);
                }
                
                // Buat handler baru
                window._orientationChangeHandler = () => {
                    setTimeout(() => {
                        updateFaceGuideOrientation();
                    }, 100); // Delay sedikit untuk memastikan orientasi sudah berubah
                };
                
                // Tambahkan listener untuk orientation change dan resize
                window.addEventListener('orientationchange', window._orientationChangeHandler);
                window.addEventListener('resize', window._orientationChangeHandler);
                
                // Update instruksi setelah camera ready
                const instruction = document.getElementById('faceInstruction');
                if (instruction) {
                    setTimeout(() => {
                        instruction.querySelector('p').innerHTML = '<span class="font-semibold text-green-700">Bagus! Tetap diam untuk hasil foto yang jelas</span>';
                    }, 2000);
                }
            }
            
            errEl?.classList.add('hidden');
            
            // ✅ PASTIKAN TOMBOL SELALU ENABLE SETELAH KAMERA READY
            if (captureBtn) {
                captureBtn.disabled = false;
                captureBtn.style.pointerEvents = 'auto';
                captureBtn.style.cursor = 'pointer';
                captureBtn.style.zIndex = '999';
                captureBtn.style.opacity = '1';
                console.log('✅ Camera ready - button enabled:', {
                    disabled: captureBtn.disabled,
                    pointerEvents: captureBtn.style.pointerEvents
                });
            }
            
            // Mulai real-time face detection monitoring setelah kamera ready (hanya untuk warning)
            setTimeout(() => {
                startFaceDetectionMonitoring();
            }, 2000); // Tunggu 2 detik setelah kamera ready
        } catch (err) {
            if (DEBUG) console.error('Camera error:', err);
            if (errEl) {
                errEl.classList.remove('hidden');
                errEl.textContent = 'Gagal mengakses kamera. Periksa izin kamera.';
            }
        }
    };

    await startCamera(currentFacing);

    switchBtn?.addEventListener('click', async () => {
        currentFacing = currentFacing === 'user' ? 'environment' : 'user';
        await startCamera(currentFacing);
    });

    // ✅ PASTIKAN TOMBOL BISA DIKLIK - SETUP SEBELUM HANDLER
    if (captureBtn) {
        // ✅ FORCE ENABLE - PASTIKAN TIDAK ADA YANG BISA DISABLE
        const forceEnableButton = () => {
            if (captureBtn) {
                captureBtn.disabled = false;
                captureBtn.removeAttribute('disabled');
                captureBtn.style.pointerEvents = 'auto';
                captureBtn.style.cursor = 'pointer';
                captureBtn.style.zIndex = '999';
                captureBtn.style.opacity = '1';
                captureBtn.style.position = 'relative';
            }
        };
        
        // Enable sekarang
        forceEnableButton();
        
        // ✅ FORCE ENABLE SETIAP 500ms - PASTIKAN TIDAK PERNAH DISABLE
        setInterval(forceEnableButton, 500);
        
        console.log('✅ Capture button setup BEFORE handler:', {
            disabled: captureBtn.disabled,
            hasDisabledAttr: captureBtn.hasAttribute('disabled'),
            pointerEvents: captureBtn.style.pointerEvents,
            zIndex: captureBtn.style.zIndex,
            opacity: captureBtn.style.opacity
        });
    }

    // Capture: gunakan canvas.toBlob untuk mengurangi ukuran payload
    // ✅ PASTIKAN TOMBOL BISA DIKLIK - SUPPORT CLICK DAN TOUCH
    const captureHandler = async (e) => {
        console.log('🔵 captureHandler called!', {
            type: e?.type,
            target: e?.target?.id,
            timestamp: new Date().toISOString()
        });
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        if (video.readyState < 2) { // HAVE_CURRENT_DATA
            if (DEBUG) console.warn('Video not ready yet');
            return;
        }
        const width = video.videoWidth || 640;
        const height = video.videoHeight || 480;
        canvas.width = width;
        canvas.height = height;
        const ctx = canvas.getContext('2d');
        if (!ctx) {
            if (DEBUG) console.error('Canvas context missing');
            return;
        }
        ctx.drawImage(video, 0, 0, width, height);

        // Deteksi wajah dan bentuk wajah menggunakan Face Detection API (satu kali deteksi untuk kedua tujuan)
        let faceCount = 0;
        let faceShape = null; // ❌ JANGAN default ke 'oval' - hanya set jika benar-benar terdeteksi
        // multipleFacesWarning sudah dideklarasikan di scope atas
        
        try {
            // Coba gunakan Face Detection API jika tersedia (Chrome/Edge)
            if (window.FaceDetector) {
                const faceDetector = new FaceDetector({ fastMode: true, maxDetectedFaces: 5 });
                const faces = await faceDetector.detect(canvas);
                faceCount = faces.length;
                
                if (DEBUG) console.log('Face Detection API result:', { faceCount, faces });
                
                // ✅ SET faceShape jika faceCount === 1 DAN ada boundingBox
                // ✅ Hanya terima 1 wajah saja
                if (faceCount === 1 && faces.length > 0 && faces[0].boundingBox) {
                    const faceBoundingBox = faces[0].boundingBox;
                    const faceWidth = faceBoundingBox.width;
                    const faceHeight = faceBoundingBox.height;
                    const faceX = faceBoundingBox.x || 0;
                    const faceY = faceBoundingBox.y || 0;
                    const faceRatio = faceWidth / faceHeight;
                    
                    // ✅ VALIDASI SANGAT FLEKSIBEL: Hanya cek apakah wajah terlihat
                    // Tidak ada validasi ketat untuk ukuran, area, atau posisi
                    // Selama wajah terdeteksi oleh API, langsung lanjut ke deteksi bentuk wajah
                    console.log('🔍 Face detected - langsung lanjut ke deteksi bentuk wajah:', {
                        faceWidth: Math.round(faceWidth),
                        faceHeight: Math.round(faceHeight),
                        faceX: Math.round(faceX),
                        faceY: Math.round(faceY),
                        faceRatio: faceRatio.toFixed(3)
                    });
                    console.log('✅ Face detection validation PASSED - wajah terlihat, scan bisa dilakukan');
                    
                    // ✅ DETEKSI BENTUK WAJAH - FLEKSIBEL (SELAMA WAJAH TERLIHAT, BISA SCAN)
                    // ✅ CATATAN: Hanya sampai sini jika validasi minimal PASSED
                    // Gunakan range yang FLEKSIBEL - selama wajah terlihat, deteksi bentuk wajah
                    // Round: rasio 0.60-0.85 (hampir bulat, lebar ≈ tinggi)
                    // Square: rasio 0.85-1.00 (hampir persegi, sedikit lebih lebar)
                    // Heart: rasio 1.00-1.20 (dahi lebih lebar dari dagu, tapi tidak terlalu panjang)
                    // Oval: rasio 1.15-1.60 (bentuk proporsional, lebih panjang dari lebar) - RANGE FLEKSIBEL
                    // Oblong: rasio > 1.60 (wajah sangat panjang)
                    
                    // ✅ PRIORITAS: Cek range yang paling spesifik dulu (dari yang paling panjang ke yang paling lebar)
                    // ✅ FLEKSIBEL: Range lebih luas agar lebih mudah terdeteksi
                    if (faceRatio > 1.45) {
                        // Wajah sangat panjang - Oblong (RANGE DIPERKETAT)
                        faceShape = 'lonjong'; // Oblong
                        console.log('🔍 Detected: Oblong (ratio > 1.45)');
                    } else if (faceRatio >= 1.30 && faceRatio <= 1.45) {
                        // Rasio sedang-tinggi - Oval (RANGE SANGAT KETAT - 1.30-1.45)
                        // ✅ RANGE DIPERKETAT LAGI agar tidak terlalu banyak yang masuk oval
                        faceShape = 'oval'; // Oval
                        console.log('🔍 Detected: Oval (ratio 1.30-1.45)');
                    } else if (faceRatio >= 1.20 && faceRatio < 1.30) {
                        // Heart shape (dahi lebih lebar dari dagu) - RANGE DIPERLUAS
                        faceShape = 'hati'; // Heart
                        console.log('🔍 Detected: Heart (ratio 1.20-1.30)');
                    } else if (faceRatio >= 1.00 && faceRatio < 1.20) {
                        // Heart shape (dahi lebih lebar dari dagu)
                        faceShape = 'hati'; // Heart
                        console.log('🔍 Detected: Heart (ratio 1.00-1.20)');
                    } else if (faceRatio >= 0.85 && faceRatio < 1.00) {
                        // Hampir persegi - Square
                        faceShape = 'kotak'; // Square
                        console.log('🔍 Detected: Square (ratio 0.85-1.00)');
                    } else if (faceRatio >= 0.60 && faceRatio < 0.85) {
                        // Hampir bulat - Round
                        faceShape = 'bulat'; // Round
                        console.log('🔍 Detected: Round (ratio 0.60-0.85)');
                    } else if (faceRatio < 0.60) {
                        // Sangat lebar - Square
                        faceShape = 'kotak'; // Square
                        console.log('🔍 Detected: Square (ratio < 0.60, very wide)');
                    } else {
                        // ❌ TIDAK MASUK RANGE MANAPUN - coba klasifikasi berdasarkan ratio
                        // ✅ JANGAN LANGSUNG DEFAULT KE OVAL - coba klasifikasi lebih akurat
                        if (faceRatio > 1.45) {
                            faceShape = 'lonjong'; // Oblong
                            console.log('🔍 Detected: Oblong (ratio > 1.45, fallback)');
                        } else if (faceRatio >= 1.20) {
                            faceShape = 'hati'; // Heart (lebih masuk akal dari oval)
                            console.log('🔍 Detected: Heart (ratio >= 1.20, fallback)');
                        } else if (faceRatio >= 1.00) {
                            faceShape = 'hati'; // Heart
                            console.log('🔍 Detected: Heart (ratio >= 1.00, fallback)');
                        } else if (faceRatio >= 0.85) {
                            faceShape = 'kotak'; // Square
                            console.log('🔍 Detected: Square (ratio >= 0.85, fallback)');
                        } else {
                            faceShape = 'bulat'; // Round
                            console.log('🔍 Detected: Round (ratio < 0.85, fallback)');
                        }
                        console.warn('⚠️ Face ratio tidak masuk range spesifik, menggunakan klasifikasi fallback:', faceRatio.toFixed(3));
                    }
                    // ❌ TIDAK ADA else default ke oval - biarkan null jika tidak jelas
                    
                    // ✅ ALWAYS LOG - tidak pakai DEBUG flag (penting untuk debugging)
                    console.log('🔍🔍🔍 ========== FACE SHAPE DETECTION RESULT ==========');
                    // ✅ Variabel faceArea, canvasArea, areaRatio sudah dideklarasikan di atas
                    
                    console.log('📐 Face Dimensions:', {
                        width: Math.round(faceWidth) + 'px',
                        height: Math.round(faceHeight) + 'px',
                        ratio: faceRatio.toFixed(3),
                        area: Math.round(faceArea) + 'px²',
                        canvasArea: Math.round(canvasArea) + 'px²',
                        areaRatio: (areaRatio * 100).toFixed(2) + '%'
                    });
                    console.log('🎯 Detected Shape:', faceShape || 'NULL');
                    console.log('📊 Classification:', faceShape ? `${faceShape} (ratio: ${faceRatio.toFixed(3)})` : 'TIDAK TERDETEKSI');
                    console.log('📋 Available Ranges:', {
                        'Oblong': '> 1.45',
                        'Oval': '1.30-1.45 ✅ (SANGAT KETAT)',
                        'Heart': '1.00-1.30',
                        'Square': '0.85-1.00',
                        'Round': '0.60-0.85',
                        'Square (wide)': '< 0.65'
                    });
                    console.log('✅ Current ratio falls in:', faceRatio.toFixed(3));
                    if (faceShape === 'oval') {
                        console.warn('⚠️⚠️⚠️ DETECTED AS OVAL - Ratio:', faceRatio.toFixed(3), 'Range: 1.35-1.50');
                    }
                    console.log('🔍🔍🔍 ============================================');
                } else {
                    // ❌ Tidak ada bounding box atau faceCount tidak 1
                    // JANGAN set faceShape - biarkan null (akan di-handle di validasi)
                    if (DEBUG) console.warn('Cannot determine face shape:', { 
                        faceCount, 
                        hasBoundingBox: faces.length > 0 && faces[0]?.boundingBox 
                    });
                }
            } else {
                // Face Detection API tidak tersedia
                // ⚠️ CATATAN: Face Detection API hanya tersedia di Chrome/Edge
                // Safari dan Firefox tidak support → tapi tetap biarkan scan, backend akan detect wajah
                console.warn('⚠️ Face Detection API not available in this browser');
                console.warn('📌 Browser yang support: Chrome, Edge (Chromium)');
                console.warn('📌 Browser yang TIDAK support: Safari, Firefox');
                console.warn('✅ Tetap lanjut scan - backend akan detect wajah menggunakan AI');
                faceCount = null; // Set null untuk menandakan tidak bisa deteksi real-time
                // ❌ JANGAN set faceShape - biarkan null, backend akan detect
                
                // ✅ TIDAK TAMPILKAN MODAL - biarkan user scan, backend akan handle deteksi wajah
                // User tetap bisa scan meskipun API tidak tersedia
                
                if (DEBUG) console.warn('Face Detection API not available, backend will detect face using AI');
            }
        } catch (err) {
            // Error saat deteksi
            console.error('❌ Face detection error:', err);
            faceCount = null; // Set null untuk menandakan error
            // ❌ JANGAN set faceShape - biarkan null
            
            if (DEBUG) console.warn('Face detection error, cannot verify face count or shape:', err);
        }

        // ✅ LOG HASIL DETEKSI SEBELUM VALIDASI
        console.log('📊📊📊 ========== FINAL FACE DETECTION RESULT ==========');
        console.log('📊 Face detection result:', {
            faceCount: faceCount,
            faceShape: faceShape || 'NULL',
            isValid: faceCount === 1 && faceShape !== null,
            timestamp: new Date().toISOString()
        });
        
        // ✅ VALIDASI: Hanya terima 1 wajah, tolak jika tidak ada wajah (0) atau lebih dari 1 wajah
        // ✅ Jika Face Detection API tidak tersedia (null), tetap biarkan scan - backend akan detect wajah
        if (faceCount === null || faceCount === undefined) {
            // ✅ FLEKSIBEL: Jika API tidak tersedia, tetap biarkan scan - backend akan handle deteksi wajah
            console.warn('⚠️ Face Detection API tidak tersedia - tetap lanjut scan, backend akan detect wajah', {
                faceCount: faceCount,
                faceShape: faceShape,
                reason: 'Face Detection API tidak tersedia di browser ini, backend akan detect wajah menggunakan AI'
            });
            
            // ✅ TIDAK TOLAK SCAN - biarkan lanjut, backend akan detect wajah
            // Hanya log warning, tidak tampilkan modal atau return
            // User tetap bisa scan meskipun API tidak tersedia
        } else if (faceCount === 0 || faceCount > 1) {
            console.error('❌❌❌ SCAN DITOLAK - Face detection tidak valid:', {
                faceCount: faceCount,
                faceShape: faceShape,
                reason: faceCount === 0 ? 'Tidak ada wajah terdeteksi (foto kosong)' : `Lebih dari 1 wajah terdeteksi: ${faceCount} wajah. Hanya 1 wajah yang diperbolehkan.`
            });
            
            // 🔥 FIX 1: HAPUS SESSION SAAT SCAN GAGAL - WAJIB!
            try {
                sessionStorage.removeItem('faceShape');
                sessionStorage.removeItem('scanImage');
                console.warn('🧹 Session cleared karena scan tidak valid', {
                    faceCount,
                    faceShape,
                    timestamp: new Date().toISOString()
                });
            } catch (err) {
                console.warn('Failed to clear sessionStorage:', err);
            }
            
            // ✅ TAMPILKAN MODAL PINK BUKAN BANNER MERAH
            if (faceCount > 1) {
                // ✅ TAMPILKAN MODAL PINK UNTUK MULTIPLE FACES (lebih dari 1)
                if (typeof showMultipleFacesModal === 'function') {
                    showMultipleFacesModal(
                        '❌ GAGAL: Terdeteksi Lebih dari 1 Wajah',
                        `AI tidak dapat menganalisis jika ada lebih dari 1 orang dalam foto. Terdeteksi ${faceCount} wajah. Pastikan hanya 1 wajah yang terlihat di kamera.`
                    );
                }
                // Sembunyikan banner merah jika ada
                if (multipleFacesWarning) {
                    multipleFacesWarning.classList.add('hidden');
                }
                return; // Exit early karena sudah tampilkan modal
            } else if (faceCount === 0) {
                // ✅ TAMPILKAN MODAL PINK UNTUK TIDAK ADA WAJAH (foto kosong)
                if (typeof showMultipleFacesModal === 'function') {
                    showMultipleFacesModal(
                        '⚠️ Wajah Tidak Terdeteksi',
                        'Tidak ada wajah yang terlihat di foto. Pastikan wajah terlihat jelas dan menghadap kamera, lalu coba lagi.'
                    );
                }
                // Sembunyikan banner merah jika ada
                if (multipleFacesWarning) {
                    multipleFacesWarning.classList.add('hidden');
                }
                return; // Exit early karena sudah tampilkan modal
            }
            
            // ⛔ Seharusnya tidak sampai sini karena semua kasus sudah di-handle di atas
            return; // Exit early
        }
        
        // ✅ Hanya sampai sini jika faceCount === 1 (hanya 1 wajah yang diperbolehkan)
        console.log('✅ Validasi berhasil, lanjutkan capture', { 
            faceCount,
            faceShape,
            timestamp: new Date().toISOString()
        });
        
        // Sembunyikan warning jika validasi berhasil
            if (multipleFacesWarning) {
                multipleFacesWarning.classList.add('hidden');
            }

        // ✅ LANGSUNG KIRIM KE BACKEND & REDIRECT - TIDAK TUNGGU PROSES LAMA
        // Tampilkan loading indicator
        if (typeof showNotification === 'function') {
            showNotification('info', 'Memproses...', 'Sedang mengirim foto ke server...', 2000);
        }

        // ✅ LANGSUNG KONVERSI CANVAS KE BLOB & KIRIM KE BACKEND
        canvas.toBlob(async (blob) => {
            if (!blob) {
                if (DEBUG) console.error('Failed to create blob from canvas');
                if (typeof showNotification === 'function') {
                    showNotification('error', 'Gagal', 'Gagal memproses foto. Silakan coba lagi.', 3000);
                }
                return;
            }
            
            try {
                // ✅ KIRIM KE BACKEND DULU, BARU REDIRECT
                // Convert blob to dataURL untuk sessionStorage dan backend
            const reader = new FileReader();
                reader.onloadend = async () => {
                const dataUrl = reader.result;
                    
                try {
                        // ✅ SIMPAN KE SESSIONSTORAGE (untuk fallback)
                    sessionStorage.setItem('scanImage', dataUrl);
                    sessionStorage.setItem('faceShape', faceShape);
                        
                        console.log('✅ Data saved to sessionStorage, sending to backend...', { faceShape });
                        
                        // ✅ KIRIM KE BACKEND VIA FORM SUBMIT (BUKAN AJAX) AGAR BACKEND SET SESSION
                        const analyzeUrl = window.__SCAN_ROUTES__?.analyze || './analyze';
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                        
                        // Buat form untuk submit ke backend
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = analyzeUrl;
                        form.enctype = 'multipart/form-data';
                        form.style.display = 'none';
                        
                        // CSRF token
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = csrfToken || '';
                        form.appendChild(csrfInput);
                        
                        // Image file - gunakan FormData untuk submit
                        // File input yang dibuat secara dinamis mungkin tidak berfungsi dengan baik
                        // Jadi kita gunakan FormData dan submit via hidden iframe atau langsung submit form
                        const file = new File([blob], 'scan.jpg', { type: 'image/jpeg' });
                        
                        // ✅ PASTIKAN DATA TERSIMPAN DENGAN BENAR SEBELUM REDIRECT
                        console.log('✅ Data saved to sessionStorage:', {
                            hasImage: !!dataUrl,
                            imageLength: dataUrl ? dataUrl.length : 0,
                            faceShape: faceShape,
                            faceShapeType: typeof faceShape,
                            timestamp: new Date().toISOString()
                        });
                        
                        // ✅ Jika faceShape null tapi wajah terdeteksi (1 wajah), tetap lanjut
                        // Backend akan handle deteksi wajah jika faceShape null
                        // ✅ CATATAN: faceCount sudah divalidasi di atas, jadi di sini pasti faceCount === 1
                        if (!faceShape && faceCount === 1) {
                            console.warn('⚠️ faceShape null tapi wajah terdeteksi - backend akan detect wajah', {
                                faceCount: faceCount,
                                faceShape: faceShape
                            });
                            // Tetap lanjut, backend akan detect wajah
                        } else if (!faceShape) {
                            // ✅ Seharusnya tidak sampai sini karena faceCount sudah divalidasi
                            // Tapi untuk safety, tetap cek
                            console.warn('⚠️ faceShape null - backend akan detect wajah', {
                                faceCount: faceCount,
                                faceShape: faceShape
                            });
                            // Tetap lanjut, backend akan detect wajah
                        }
                        
                        // ✅ KIRIM KE BACKEND VIA FORMDATA
                        const formData = new FormData();
                        formData.append('image', blob, 'scan.jpg');
                        // ✅ Kirim faceShape meskipun null - backend akan handle
                        formData.append('face_shape', faceShape || '');
                        formData.append('_token', csrfToken || '');
                        
                        console.log('✅ Sending to backend and redirecting...', { 
                            faceShape,
                            blobSize: blob.size,
                            hasToken: !!csrfToken,
                            analyzeUrl
                        });
                        
                        // ✅ KIRIM KE BACKEND & TUNGGU RESPONSE SELESAI
                        // Tunggu response dari backend untuk memastikan analisis selesai dan session tersimpan
                        try {
                            const response = await fetch(analyzeUrl, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': csrfToken || '',
                                },
                                body: formData
                            });
                            
                            // ✅ CEK STATUS RESPONSE SEBELUM PARSE JSON
                            if (!response.ok) {
                                const errorText = await response.text();
                                console.error('❌ Backend returned error:', {
                                    status: response.status,
                                    statusText: response.statusText,
                                    error: errorText.substring(0, 200)
                                });
                                throw new Error(`Backend error: ${response.status} - ${errorText.substring(0, 100)}`);
                            }
                            
                            // ✅ CEK CONTENT-TYPE SEBELUM PARSE JSON
                            const contentType = response.headers.get('content-type');
                            if (!contentType || !contentType.includes('application/json')) {
                                const text = await response.text();
                                console.error('❌ Backend returned non-JSON response:', {
                                    contentType: contentType,
                                    preview: text.substring(0, 200)
                                });
                                throw new Error('Backend returned non-JSON response');
                            }
                            
                            const result = await response.json();
                            
                            console.log('✅ Backend analysis completed', {
                                status: response.status,
                                ok: response.ok,
                                has_face_shape: !!result.face_shape,
                                has_recommendations: !!(result.recommendations && result.recommendations.length > 0)
                            });
                            
                            // ✅ REDIRECT SETELAH ANALISIS SELESAI
                            // Session sudah tersimpan di backend, langsung redirect
                            const resultsUrl = window.__SCAN_ROUTES__?.results || './results';
                            console.log('✅ Redirecting to results page...', resultsUrl);
                            window.location.href = resultsUrl;
                            
                        } catch (err) {
                            console.error('❌ Error sending to backend:', err);
                            // ✅ TAMPILKAN NOTIFIKASI ERROR JIKA ADA
                            if (typeof showNotification === 'function') {
                                showNotification('error', 'Gagal', 'Gagal mengirim foto ke server. Menggunakan data lokal...', 5000);
                            }
                            // Tetap redirect meskipun error (fallback ke sessionStorage)
                            const resultsUrl = window.__SCAN_ROUTES__?.results || './results';
                            window.location.href = resultsUrl;
                        }
                        
                    } catch (err) {
                        console.error('❌ Error preparing form:', err);
                        if (typeof showNotification === 'function') {
                            showNotification('error', 'Gagal', 'Gagal memproses foto. Silakan coba lagi.', 5000);
                        }
                    }
            };
            reader.onerror = () => {
                if (DEBUG) console.error('FileReader error');
                    if (typeof showNotification === 'function') {
                        showNotification('error', 'Gagal', 'Gagal membaca foto. Silakan coba lagi.', 3000);
                    }
            };
            reader.readAsDataURL(blob);
            } catch (err) {
                console.error('❌ Error sending to backend:', err);
                if (typeof showNotification === 'function') {
                    showNotification('error', 'Gagal', 'Gagal mengirim foto ke server. Silakan coba lagi.', 5000);
                }
            }
        }, 'image/jpeg', 0.8);
    };
    
    // ✅ TAMBAHKAN EVENT LISTENER UNTUK CLICK DAN TOUCH
    if (captureBtn) {
        // ✅ FORCE ENABLE TOMBOL - PASTIKAN BISA DIKLIK
        captureBtn.disabled = false;
        captureBtn.style.pointerEvents = 'auto';
        captureBtn.style.cursor = 'pointer';
        captureBtn.style.zIndex = '999';
        captureBtn.style.opacity = '1';
        captureBtn.style.position = 'relative';
        
        // Hapus listener lama jika ada
        const oldHandler = captureBtn._captureHandler;
        if (oldHandler) {
            captureBtn.removeEventListener('click', oldHandler);
            captureBtn.removeEventListener('touchend', oldHandler);
        }
        
        // Simpan handler untuk bisa dihapus nanti
        captureBtn._captureHandler = captureHandler;
        
        // Tambahkan event listener untuk click dan touch
        captureBtn.addEventListener('click', captureHandler, { passive: false });
        captureBtn.addEventListener('touchend', captureHandler, { passive: false });
        
        console.log('✅✅✅ Capture button event listeners attached:', {
            hasClickHandler: true,
            hasTouchHandler: true,
            disabled: captureBtn.disabled,
            pointerEvents: captureBtn.style.pointerEvents,
            zIndex: captureBtn.style.zIndex,
            opacity: captureBtn.style.opacity,
            cursor: captureBtn.style.cursor
        });
        
        // ✅ TEST CLICK - Pastikan event listener bekerja
        captureBtn.addEventListener('click', (e) => {
            console.log('🔵 Capture button clicked!', {
                disabled: captureBtn.disabled,
                pointerEvents: captureBtn.style.pointerEvents,
                timestamp: new Date().toISOString()
            });
        }, { once: true }); // Test sekali saja
        
        console.log('✅ Capture button event listeners attached', {
            disabled: captureBtn.disabled,
            pointerEvents: captureBtn.style.pointerEvents,
            zIndex: captureBtn.style.zIndex,
            hasHandler: !!captureBtn._captureHandler,
            buttonElement: captureBtn
        });
    } else {
        console.error('❌ Capture button not found!');
    }

    // Cleanup stream saat page unload
    window.addEventListener('pagehide', () => {
        stopFaceDetectionMonitoring(); // Hentikan monitoring
        if (activeStream) {
            activeStream.getTracks().forEach(t => t.stop());
            activeStream = null;
        }
    });
});

// Notification function untuk menampilkan alert/notifikasi
const showNotification = (type, title, message, duration = 5000) => {
    const toast = document.getElementById('notificationToast');
    const toastIcon = document.getElementById('toastIcon');
    const toastTitle = document.getElementById('toastTitle');
    const toastMessage = document.getElementById('toastMessage');
    const toastClose = document.getElementById('toastClose');
    
    if (!toast) return;
    
    const toastContainer = toast.querySelector('#toastContainer');
    
    // Set icon dan border berdasarkan type
    if (type === 'success') {
        toastIcon.innerHTML = '<div class="h-8 w-8 rounded-full grid place-items-center bg-green-100 text-green-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>';
        if (toastContainer) {
            toastContainer.classList.remove('border-red-300', 'border-yellow-300', 'border-blue-300');
            toastContainer.classList.add('border-green-300');
        }
    } else if (type === 'error') {
        toastIcon.innerHTML = '<div class="h-8 w-8 rounded-full grid place-items-center bg-red-100 text-red-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></div>';
        if (toastContainer) {
            toastContainer.classList.remove('border-green-300', 'border-yellow-300', 'border-blue-300');
            toastContainer.classList.add('border-red-300');
        }
    } else if (type === 'warning') {
        toastIcon.innerHTML = '<div class="h-8 w-8 rounded-full grid place-items-center bg-yellow-100 text-yellow-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></div>';
        if (toastContainer) {
            toastContainer.classList.remove('border-green-300', 'border-red-300', 'border-blue-300');
            toastContainer.classList.add('border-yellow-300');
        }
    } else {
        toastIcon.innerHTML = '<div class="h-8 w-8 rounded-full grid place-items-center bg-blue-100 text-blue-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div>';
        if (toastContainer) {
            toastContainer.classList.remove('border-green-300', 'border-red-300', 'border-yellow-300');
            toastContainer.classList.add('border-blue-300');
        }
    }
    
    toastTitle.textContent = title;
    toastMessage.textContent = message;
    
    // Show toast dengan pointer-events
    toast.classList.remove('hidden');
    toast.classList.add('block');
    toast.style.pointerEvents = 'auto';
    toast.style.zIndex = '9999';
    
    // Pastikan container juga bisa diklik
    if (toastContainer) {
        toastContainer.style.pointerEvents = 'auto';
        toastContainer.style.cursor = 'pointer';
    }
    
    // Auto hide setelah duration
    const autoHide = setTimeout(() => {
        hideNotification();
    }, duration);
    
    // ✅ HAPUS EVENT LISTENER LAMA JIKA ADA (untuk menghindari duplicate)
    const oldCloseHandler = toastClose._closeHandler;
    if (oldCloseHandler) {
        toastClose.removeEventListener('click', oldCloseHandler);
        toastClose.removeEventListener('touchend', oldCloseHandler);
    }
    
    // Close button handler - support click dan touch
    const closeHandler = (e) => {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        clearTimeout(autoHide);
        hideNotification();
    };
    
    // Simpan handler untuk bisa dihapus nanti
    toastClose._closeHandler = closeHandler;
    
    // Support multiple event types untuk mobile
    toastClose.addEventListener('click', closeHandler, { passive: false });
    toastClose.addEventListener('touchend', closeHandler, { passive: false });
    
    // ✅ BISA DIKLIK SELURUH TOAST UNTUK TUTUP (mobile-friendly)
    const oldToastHandler = toastContainer._toastHandler;
    if (oldToastHandler) {
        toastContainer.removeEventListener('click', oldToastHandler);
        toastContainer.removeEventListener('touchend', oldToastHandler);
    }
    
    const toastClickHandler = (e) => {
        // Jangan tutup jika klik di tombol close (biarkan closeHandler yang handle)
        if (e.target && e.target.closest && e.target.closest('#toastClose')) {
            return;
        }
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        clearTimeout(autoHide);
        hideNotification();
    };
    
    toastContainer._toastHandler = toastClickHandler;
    toastContainer.addEventListener('click', toastClickHandler, { passive: false });
    toastContainer.addEventListener('touchend', toastClickHandler, { passive: false });
};

const hideNotification = () => {
    const toast = document.getElementById('notificationToast');
    if (toast) {
        toast.classList.add('hidden');
        toast.classList.remove('block');
        toast.style.pointerEvents = 'none';
    }
};

// Results page image preview
document.addEventListener('DOMContentLoaded', () => {
    // ✅ CEK DULU SEBELUM LOG - HANYA JALAN DI RESULTS PAGE
    const cameraPage = document.getElementById('scanCameraPage');
    const page = document.getElementById('scanResultPage');
    
    // ✅ JIKA DI CAMERA PAGE, JANGAN JALANKAN SCRIPT RESULTS (EXIT SEBELUM LOG)
    if (cameraPage) {
        return; // Exit early jika di camera page - TIDAK LOG APAPUN
    }
    
    if (!page) {
        // ✅ JANGAN LOG ERROR - INI NORMAL JIKA BUKAN RESULTS PAGE
        return; // Exit early jika bukan results page
    }
    
    // ✅ HANYA LOG JIKA BENAR-BENAR DI RESULTS PAGE
    console.log('🎬🎬🎬 DOMContentLoaded - Results page script LOADED!');
    console.log('📍 Current page:', window.location.href);
    console.log('✅✅✅ scanResultPage found, continuing...');

    const dataUrl = sessionStorage.getItem('scanImage');
    const faceShape = sessionStorage.getItem('faceShape');
    
    // ✅ LOG DATA DARI SESSIONSTORAGE UNTUK DEBUG
    console.log('📋 Data from sessionStorage:', {
        hasDataUrl: !!dataUrl,
        dataUrlLength: dataUrl ? dataUrl.length : 0,
        faceShape: faceShape,
        faceShapeType: typeof faceShape,
        allKeys: Object.keys(sessionStorage),
        scanImage: dataUrl ? dataUrl.substring(0, 50) + '...' : 'null',
        faceShapeValue: faceShape || 'null'
    });
    
    // ✅ FLEKSIBEL: Cek apakah data ada di Laravel session (sudah di-render di blade)
    // Jika tidak ada di sessionStorage, coba load dari Laravel session atau tetap lanjut analyze
    // Cek apakah ada gambar yang sudah di-render oleh Laravel (bukan dari sessionStorage)
    const laravelImage = document.querySelector('img[src*="scans/"]') || document.querySelector('img[src*="storage/scans/"]');
    const hasLaravelSession = !!laravelImage;
    
    if (!dataUrl || !faceShape) {
        console.warn('⚠️ Data tidak ada di sessionStorage, cek Laravel session...', {
            hasDataUrl: !!dataUrl,
            hasFaceShape: !!faceShape,
            hasLaravelSession: !!hasLaravelSession,
            allSessionKeys: Object.keys(sessionStorage),
            timestamp: new Date().toISOString()
        });
        
        // ✅ JIKA ADA DATA DI LARAVEL SESSION, GUNAKAN ITU
        if (hasLaravelSession) {
            console.log('✅ Data ditemukan di Laravel session, menggunakan data dari server');
            // Data sudah di-render di blade, tidak perlu load dari sessionStorage
            // Tetap lanjut analyze dengan data dari Laravel session
            // ✅ AMBIL GAMBAR DARI LARAVEL SESSION (sudah di-render di blade)
            const laravelImage = document.getElementById('captureImage');
            if (laravelImage && laravelImage.src) {
                // Convert image src ke dataUrl jika perlu
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = function() {
                    canvas.width = img.width;
                    canvas.height = img.height;
                    ctx.drawImage(img, 0, 0);
                    const newDataUrl = canvas.toDataURL('image/jpeg', 0.8);
                    // Update dataUrl untuk digunakan di analyze()
                    dataUrl = newDataUrl;
                    sessionStorage.setItem('scanImage', newDataUrl);
                    console.log('✅ Image converted from Laravel session to dataUrl');
                };
                img.src = laravelImage.src;
            }
        } else {
            // ✅ JIKA TIDAK ADA DI MANA-MANA, BARU REDIRECT
            console.error('❌ Data tidak ditemukan di sessionStorage maupun Laravel session');
            
            // ✅ HIDE LOADING JIKA DATA TIDAK ADA
            const loading = document.getElementById('loadingAnalysis');
            if (loading) {
                loading.classList.add('hidden');
            }
            
            // ✅ TAMPILKAN ERROR & REDIRECT
            if (typeof showNotification === 'function') {
                showNotification(
                    'error',
                    'Data Tidak Ditemukan',
                    'Data scan tidak ditemukan. Silakan scan ulang.',
                    5000
                );
            }
            
            // ✅ REDIRECT KE CAMERA SETELAH 2 DETIK
            setTimeout(() => {
                const scanUrl = window.__SCAN_ROUTES__?.camera || './camera';
                console.log('🔄 Redirecting to camera page...', scanUrl);
                window.location.href = scanUrl;
            }, 2000);
            
            return; // ⛔ STOP - jangan lanjutkan jika data tidak ada
        }
    }
    
    console.log('✅ Data from sessionStorage is valid, proceeding with analyze()...', {
        faceShape: faceShape,
        dataUrlLength: dataUrl ? dataUrl.length : 0
    });
    
    // Normalize to API expected enum (untuk API call)
    const normalizeFaceShapeForApi = (s) => {
        if (!s) return null; // Jangan default ke 'Oval'
        const v = (s || '').toLowerCase();
        if (v === 'oval') return 'Oval';
        if (v === 'bulat' || v === 'round') return 'Round';
        if (v === 'lonjong' || v === 'oblong') return 'Oblong';
        if (v === 'square' || v === 'kotak') return 'Square';
        if (v === 'heart' || v === 'hati') return 'Heart';
        return null; // ❌ Jangan default ke 'Oval' - return null jika tidak valid
    };
    
    // Normalize untuk display (user-friendly)
    const normalizeFaceShapeForDisplay = (s) => {
        if (!s) return 'Tidak terdeteksi';
        const v = (s || '').toLowerCase();
        const shapeMap = {
            'oval': 'Oval',
            'bulat': 'Bulat',
            'round': 'Bulat',
            'lonjong': 'Lonjong',
            'oblong': 'Lonjong',
            'square': 'Kotak',
            'kotak': 'Kotak',
            'heart': 'Hati',
            'hati': 'Hati'
        };
        return shapeMap[v] || 'Tidak terdeteksi';
    };
    
    const apiFaceShape = normalizeFaceShapeForApi(faceShape);
    const displayFaceShape = normalizeFaceShapeForDisplay(faceShape);
    const userName = sessionStorage.getItem('userName') || '';
    const userPhone = sessionStorage.getItem('userPhone') || '';
    const prefLength = sessionStorage.getItem('prefLength') || '';
    const prefType = sessionStorage.getItem('prefType') || '';
    const prefCondition = sessionStorage.getItem('prefCondition') || '';
    
    // Debug: Log semua data dari sessionStorage
    if (DEBUG) {
        console.log('📋 Data dari sessionStorage:', {
            hasImage: !!dataUrl,
            faceShape: faceShape || 'tidak ada',
            userName: userName || 'kosong',
            userPhone: userPhone || 'kosong',
            prefLength: prefLength || 'kosong',
            prefType: prefType || 'kosong',
            prefCondition: prefCondition || 'kosong'
        });
    }
    // ✅ TAMPILKAN FOTO DARI SESSIONSTORAGE (FALLBACK JIKA LARAVEL SESSION TIDAK ADA)
    // ✅ WAJIB TAMPILKAN FOTO - baik dari Laravel session atau sessionStorage
    console.log('🖼️ Checking for image to display...', {
        hasDataUrl: !!dataUrl,
        dataUrlLength: dataUrl ? dataUrl.length : 0,
        allSessionKeys: Object.keys(sessionStorage)
    });
    
    if (dataUrl) {
        const wrap = document.getElementById('capturePreview');
        const img = document.getElementById('captureImage');
        
        if (wrap && img) {
            // ✅ SET IMAGE SRC DAN TAMPILKAN
            img.src = dataUrl;
            wrap.classList.remove('hidden');
            console.log('✅✅✅ Foto ditampilkan dari sessionStorage:', {
                hasImage: !!dataUrl,
                imageLength: dataUrl ? dataUrl.length : 0,
                elementFound: !!(wrap && img),
                imgSrc: img.src ? img.src.substring(0, 50) + '...' : 'null'
            });
            
            // ✅ PASTIKAN ELEMENT TERLIHAT
            wrap.style.display = 'block';
            wrap.style.visibility = 'visible';
            img.style.display = 'block';
            img.style.visibility = 'visible';
        } else {
            // ✅ ELEMEN TIDAK ADA - INI NORMAL JIKA LARAVEL SESSION ADA
            // Foto sudah di-render oleh Laravel, tidak perlu tampilkan dari sessionStorage
            console.log('ℹ️ Element capturePreview tidak ditemukan - foto sudah di-render oleh Laravel session', {
                hasDataUrl: !!dataUrl,
                hasLaravelImage: !!document.querySelector('img[src*="scans/"]') || !!document.querySelector('img[src*="storage/scans/"]'),
                hasWrap: !!wrap,
                hasImg: !!img
            });
        }
    } else {
        console.warn('⚠️ Tidak ada dataUrl dari sessionStorage untuk ditampilkan', {
            allSessionKeys: Object.keys(sessionStorage),
            scanImage: sessionStorage.getItem('scanImage') ? 'EXISTS' : 'NOT FOUND'
        });
    }

    // Fetch AI recommendations
    const tokenEl = document.querySelector('meta[name="csrf-token"]');
    const csrf = tokenEl ? (tokenEl.getAttribute('content') || tokenEl.content) : '';
    const loading = document.getElementById('loadingAnalysis');
    const list = document.getElementById('recommendations');
    if (!list) {
        console.error('❌ CRITICAL: recommendations element not found!');
        console.log('🔍 Available elements:', {
            hasLoading: !!loading,
            hasList: !!list,
            bodyChildren: document.body.children.length
        });
        return;
    }
    console.log('✅ recommendations element found, continuing...');

    // Helper untuk membentuk URL gambar absolut
    const assetBase = (window.__ASSET_BASE__ || window.location.origin).replace(/\/$/, '');
    const resolveAsset = (u) => {
        if (!u) return '';
        if (u.startsWith('http') || u.startsWith('data:')) return u;
        if (u.startsWith('/')) return u;
        return `${assetBase}/${u.replace(/^\//,'')}`;
    };


    // 🔥 FIX 2: JANGAN TAMPILKAN BADGE JIKA FACE INVALID
    // Hanya tampilkan badge jika apiFaceShape valid (tidak null)
    if (apiFaceShape) {
    const shapeBadge = document.createElement('div');
    shapeBadge.className = 'mt-3 sm:mt-4 inline-flex items-center gap-1.5 sm:gap-2 rounded-xl bg-pink-100 px-2.5 sm:px-3 py-1.5 sm:py-2 text-[10px] sm:text-xs text-stone-700';
        shapeBadge.innerHTML = `<span class="h-1.5 w-1.5 sm:h-2 sm:w-2 rounded-full bg-pink-400 flex-shrink-0"></span> Bentuk wajah terdeteksi: <strong>${displayFaceShape}</strong>`;
    list.before(shapeBadge);
    } else {
        console.warn('⚠️ Badge tidak ditampilkan karena apiFaceShape tidak valid:', {
            faceShape,
            apiFaceShape,
            displayFaceShape
        });
    }

    const render = (items, aiEnabled = false) => {
        list.innerHTML = '';
        items.forEach((it, index) => {
            const card = document.createElement('div');
            card.className = 'rounded-xl bg-white shadow-md hover:shadow-xl px-2.5 sm:px-3 py-2.5 sm:py-3 touch-manipulation cursor-pointer active:scale-[0.98] transition-all duration-300 hover:scale-105 hover:-translate-y-1 group';
            card.style.opacity = '0';
            card.style.transform = 'scale(0.9)';
            
            // AI badge jika AI enabled dan item direkomendasikan AI
            const aiBadge = (aiEnabled && it.ai_recommended) 
                ? '<div class="absolute top-2 left-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white text-[8px] sm:text-[10px] px-2 py-1 rounded-full font-semibold flex items-center gap-1 shadow-lg"><svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>AI</div>'
                : '';
            
            card.innerHTML = `
                <div class="h-28 sm:h-36 rounded-lg bg-stone-200 overflow-hidden relative">
                    <img src="${it.image_url || '/img/model1.png'}" alt="${it.name || 'Model Rambut'}" class="h-28 sm:h-36 w-full object-cover transition-transform duration-500 group-hover:scale-110" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    ${aiBadge}
                </div>
                <p class="mt-2 text-center text-xs sm:text-sm leading-tight px-1 font-medium group-hover:text-pink-600 transition-colors duration-300">${it.name || ''}</p>
            `;
            list.appendChild(card);
            
            // Staggered animation
            setTimeout(() => {
                card.style.transition = 'all 0.5s ease-out';
                card.style.opacity = '1';
                card.style.transform = 'scale(1)';
            }, index * 100);

        });
    };

    // Helper untuk mengirim gambar: gunakan FormData jika payload terlalu besar
    // ✅ Parameter faceShape adalah apiFaceShape yang sudah dinormalisasi
    const sendImage = async (imageDataUrl, apiFaceShapeParam, userName, userPhone, prefLength, prefType, prefCondition) => {
        const SIZE_LIMIT = 1024 * 700; // ~700KB
        // ✅ PASTIKAN ROUTE ANALYZE ADA - GUNAKAN ABSOLUTE URL JIKA TIDAK ADA
        const analyzeUrl = (window.__SCAN_ROUTES__ && window.__SCAN_ROUTES__.analyze) 
            ? window.__SCAN_ROUTES__.analyze 
            : (window.location.origin + '/scan/analyze'); // ✅ FALLBACK KE ABSOLUTE URL
        
        console.log('🌐 sendImage - Analyze URL:', analyzeUrl);
        
        // Estimate base64 size: length * 3/4
        if (imageDataUrl && imageDataUrl.length * 3 / 4 > SIZE_LIMIT) {
            // Upload blob via FormData untuk payload besar
            try {
                const blob = await (await fetch(imageDataUrl)).blob();
                const fd = new FormData();
                fd.append('image', blob, 'capture.jpg');
                // ✅ Kirim face_shape meskipun null - backend akan detect wajah jika null
                fd.append('face_shape', apiFaceShapeParam || '');
                fd.append('user_name', userName || '');
                fd.append('user_phone', userPhone || '');
                fd.append('pref[length]', prefLength || '');
                fd.append('pref[type]', prefType || '');
                fd.append('pref[condition]', prefCondition || '');
                
                const resp = await fetch(analyzeUrl, {
                    method: 'POST',
                    body: fd,
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    }
                });
                return resp;
            } catch (err) {
                if (DEBUG) console.warn('FormData upload failed, falling back to JSON:', err);
                // Fallback ke JSON jika FormData gagal
            }
        }
        
        // Gunakan JSON untuk payload kecil atau fallback
        const payload = {
            image: imageDataUrl || '',
                // ✅ Kirim face_shape meskipun null - backend akan detect wajah jika null
            face_shape: apiFaceShapeParam || '', // Backend akan detect wajah jika kosong
            user_name: userName || '',
            user_phone: userPhone || '',
            pref: {
                length: prefLength || '',
                type: prefType || '',
                condition: prefCondition || '',
            },
        };
        
        return fetch(analyzeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload),
        });
    };

    const analyze = async () => {
        // ✅ LOG DETAIL UNTUK DEBUG
        console.log('🔍 analyze() called with:', {
            faceShape,
            apiFaceShape,
            hasImage: !!dataUrl,
            dataUrlLength: dataUrl ? dataUrl.length : 0,
            normalizeResult: normalizeFaceShapeForApi(faceShape)
        });
        
        // ✅ FLEKSIBEL: Jika apiFaceShape null, tetap lanjut analyze - backend akan detect wajah
        if (!apiFaceShape) {
            console.warn('⚠️ apiFaceShape null - tetap lanjut analyze, backend akan detect wajah menggunakan AI', {
                faceShape,
                apiFaceShape,
                hasImage: !!dataUrl,
                normalized: normalizeFaceShapeForApi(faceShape)
            });
            // ✅ TIDAK BLOKIR - biarkan backend detect wajah menggunakan AI
            // Tetap lanjut analyze meskipun apiFaceShape null
        } else {
            console.log('✅ apiFaceShape valid, proceeding with analyze...', apiFaceShape);
        }
        
        // ALWAYS LOG - tidak pakai DEBUG flag
        console.log('🚀🚀🚀 ANALYZE FUNCTION CALLED - STARTING NOW!');
        console.log('📋 Session data:', {
            hasImage: !!dataUrl,
            hasFaceShape: !!faceShape,
            apiFaceShape: apiFaceShape,
            userName: userName || 'KOSONG',
            userPhone: userPhone || 'KOSONG'
        });
        
        try {
            if (DEBUG) {
                console.log('📤 Sending analyze request:', {
                    hasImage: !!dataUrl,
                    face_shape: apiFaceShape,
                    user_name: userName || 'kosong',
                    user_phone: userPhone || 'kosong',
                    preferences: {
                        length: prefLength || 'kosong',
                        type: prefType || 'kosong',
                        condition: prefCondition || 'kosong'
                    }
                });
            }
            
            const analyzeUrl = (window.__SCAN_ROUTES__ && window.__SCAN_ROUTES__.analyze) ? window.__SCAN_ROUTES__.analyze : '../analyze';
            // ALWAYS LOG - tidak pakai DEBUG flag
            console.log('🌐🌐🌐 Analyze URL:', analyzeUrl);
            console.log('🔑 CSRF Token:', csrf ? 'EXISTS' : 'MISSING');
            console.log('📤📤📤 Sending request NOW...');
            const resp = await sendImage(dataUrl, apiFaceShape, userName, userPhone, prefLength, prefType, prefCondition);
            
            // ALWAYS LOG response status
            console.log('📥 HTTP Response received:', {
                status: resp.status,
                statusText: resp.statusText,
                ok: resp.ok,
                contentType: resp.headers.get('content-type') || 'unknown'
            });
            
            // Handle CORS headers dengan try/catch
            let responseHeaders = {};
            try {
                responseHeaders = Object.fromEntries(resp.headers.entries());
            } catch (err) {
                console.warn('⚠️ Cannot read response headers (CORS restriction):', err);
            }
            
            if (!resp.ok) {
                const errorText = await resp.text();
                if (DEBUG) console.error('❌ HTTP error!', resp.status, errorText);
                throw new Error(`HTTP error! status: ${resp.status} - ${errorText}`);
            }
            
            // Error handling JSON parse dengan try/catch
            let json;
            try {
                const text = await resp.text();
                console.log('📄 Response text length:', text.length);
                json = JSON.parse(text);
                console.log('✅✅✅ JSON parsed successfully');
            } catch (parseErr) {
                console.error('❌❌❌ JSON PARSE ERROR:', parseErr);
                throw new Error('Invalid JSON response from server');
            }
            
            // ALWAYS LOG RESPONSE (tidak pakai DEBUG flag)
            console.log('📥📥📥 Analyze response received:', {
                ok: json.ok,
                saved: json.saved,
                saved_id: json.saved_id,
                save_error: json.save_error,
                recommendations_count: json.recommendations?.length || 0,
                ai_enabled: json.ai_enabled,
                ai_enabled_type: typeof json.ai_enabled,
                // ✅ LOG face_shape untuk debugging - PASTI TAMPIL DI CONSOLE
                face_shape: json.face_shape,
                face_shape_detected: json.face_shape_detected,
                face_shape_user_input: json.face_shape_user_input,
                all_keys: Object.keys(json)
            });
            
            // Tampilkan pesan jika data berhasil disimpan
            if (json.saved && json.saved_id) {
                showNotification('success', 'Data Berhasil Disimpan!', 
                    `Data Anda telah tersimpan dengan ID: ${json.saved_id}`, 4000);
                if (DEBUG) {
                    console.log('✅ Data berhasil disimpan ke database dengan ID:', json.saved_id);
                    console.log('📊 Data yang disimpan:', {
                        name: json.debug?.name_saved || userName || 'Pengguna',
                        phone: json.debug?.phone_saved || userPhone || '-',
                        face_shape: apiFaceShape,
                        recommendations: json.recommendations?.map(r => r.name).join(', ') || 'Tidak ada'
                    });
                    if (json.debug) {
                        console.log('🔍 Debug info:', json.debug);
                    }
                }
            } else if (json.save_error) {
                const errorMsg = json.save_error || 'Terjadi kesalahan saat menyimpan data';
                showNotification('error', 'Gagal Menyimpan Data!', 
                    `Error: ${errorMsg}. Silakan coba lagi atau hubungi admin.`, 8000);
                console.error('❌ Gagal menyimpan data ke database:', json.save_error);
                if (DEBUG) console.error('❌ Error details:', json);
            } else {
                showNotification('warning', 'Data Tidak Tersimpan!', 
                    'Data tidak berhasil disimpan ke database. Rekomendasi tetap ditampilkan, namun data tidak tersimpan untuk laporan.', 6000);
                if (DEBUG) {
                    console.warn('⚠️ Data tidak tersimpan (saved: false, tidak ada error)');
                    console.warn('⚠️ Response details:', json);
                }
            }
            
            loading?.classList.add('hidden');
            
            // RENDER REKOMENDASI DULU
            console.log('🔍🔍🔍 Checking if should render recommendations...', {
                'has_json': !!json,
                'json_ok': json?.ok,
                'has_recommendations': !!(json?.recommendations),
                'recommendations_length': json?.recommendations?.length || 0
            });
            
            // ✅ STEP 4: DI JS JANGAN ADA DEFAULT OVAL
            // Jika json.ok === false, tampilkan error dan jangan render apapun
            if (json && json.ok === false) {
                console.error('🚫 Analyze failed - response ok: false', {
                    error: json.error,
                    message: json.message,
                    face_shape: json.face_shape
                });
                
                if (typeof showNotification === 'function') {
                    showNotification(
                        'error',
                        'Scan Gagal',
                        json.message || 'Scan wajah tidak valid. Silakan scan ulang.',
                        6000
                    );
                }
                
                // Hapus session yang tidak valid
                try {
                    sessionStorage.clear();
                    console.warn('🧹 Session cleared due to analyze failure');
                } catch (err) {
                    console.warn('Failed to clear sessionStorage:', err);
                }
                
                // Jangan render apapun jika analyze gagal
                return;
            }
            
            // ✅ RENDER REKOMENDASI - PASTIKAN SELALU DIPANGGIL
            if (json && json.ok && json.recommendations && json.recommendations.length > 0) {
                console.log('✅✅✅ Rendering recommendations...', {
                    'count': json.recommendations.length,
                    'ai_enabled': json.ai_enabled,
                    'ai_enabled_type': typeof json.ai_enabled,
                    'recommendations': json.recommendations
                });
                
                // ✅ PASTIKAN LIST ELEMENT ADA SEBELUM RENDER
                if (!list) {
                    console.error('❌ List element not found! Cannot render recommendations.');
                    return false;
                }
                
                // Render recommendations
                console.log('🎨 Calling render() function...');
                render(json.recommendations, json.ai_enabled || false);
                console.log('✅ render() called successfully');
                
                // ✅ HIDE LOADING SETELAH RENDER
                if (loading) {
                    loading.classList.add('hidden');
                    console.log('✅ Loading hidden');
                }
                
                // CHECK AI ENABLED - SIMPLE & DIRECT
                console.log('🔍🔍🔍 Starting AI check...', {
                    'ai_enabled_raw': json.ai_enabled,
                    'ai_enabled_type': typeof json.ai_enabled,
                    'all_json_keys': Object.keys(json)
                });
                
                const aiEnabled = json.ai_enabled === true || json.ai_enabled === 'true' || json.ai_enabled === 1;
                console.log('🤖🤖🤖 AI Check result:', {
                    'raw': json.ai_enabled,
                    'type': typeof json.ai_enabled,
                    'enabled': aiEnabled,
                    'will_create_badge': aiEnabled
                });
                
                // INSERT BADGE JIKA AI ENABLED - GUNAKAN setTimeout UNTUK PASTIKAN DOM READY
                if (aiEnabled) {
                    console.log('🎯🎯🎯 AI ENABLED - Creating badge NOW!');
                    
                    // Remove existing badge
                    const existing = document.querySelector('.ai-powered-badge');
                    if (existing) existing.remove();
                    
                    // Create badge
                    const badge = document.createElement('div');
                    badge.className = 'ai-powered-badge mt-2 mb-2 inline-flex items-center gap-1.5 sm:gap-2 rounded-xl bg-gradient-to-r from-purple-100 to-pink-100 px-2.5 sm:px-3 py-1.5 sm:py-2 text-[10px] sm:text-xs text-stone-700 border border-purple-200';
                    badge.innerHTML = `<svg class="w-3 h-3 sm:w-4 sm:h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg> <span class="font-semibold text-purple-700">AI-Powered</span> <span class="text-stone-600">Rekomendasi</span>`;
                    
                    // Insert badge - GUNAKAN setTimeout UNTUK PASTIKAN DOM READY
                    setTimeout(() => {
                        console.log('⏰ setTimeout executed - attempting to insert badge...');
                        const listEl = document.getElementById('recommendations');
                        console.log('🔍 List element check:', {
                            'found': !!listEl,
                            'has_parent': !!(listEl && listEl.parentNode),
                            'list_id': listEl?.id,
                            'list_tag': listEl?.tagName
                        });
                        
                        if (listEl && listEl.parentNode) {
                            listEl.parentNode.insertBefore(badge, listEl);
                            console.log('✅✅✅ Badge SUCCESSFULLY inserted before list!');
                            console.log('📍 Badge element:', badge);
                            console.log('📍 Badge parent:', badge.parentElement);
                        } else {
                            console.warn('⚠️ List not found, trying fallback...');
                            // Fallback: insert setelah heading
                            const heading = document.querySelector('h3');
                            console.log('🔍 Heading check:', {
                                'found': !!heading,
                                'has_parent': !!(heading && heading.parentNode),
                                'heading_text': heading?.textContent?.substring(0, 50)
                            });
                            
                            if (heading && heading.parentNode) {
                                heading.parentNode.insertBefore(badge, heading.nextSibling);
                                console.log('✅✅✅ Badge inserted after heading (fallback)');
                            } else {
                                // Last resort: append ke list container
                                const container = listEl?.parentElement || document.querySelector('main') || document.body;
                                console.log('🔍 Container check:', {
                                    'found': !!container,
                                    'container_tag': container?.tagName
                                });
                                
                                if (container) {
                                    container.insertBefore(badge, listEl || container.firstChild);
                                    console.log('✅✅✅ Badge inserted in container (last resort)');
                                } else {
                                    console.error('❌❌❌ Cannot insert badge - no container found');
                                }
                            }
                        }
                    }, 100); // Wait 100ms untuk pastikan DOM ready
                } else {
                    console.log('⚠️⚠️⚠️ AI disabled - badge will NOT be created', {
                        'ai_enabled_value': json.ai_enabled,
                        'ai_enabled_type': typeof json.ai_enabled,
                        'check_result': aiEnabled
                    });
                }
            } else {
                console.warn('⚠️⚠️⚠️ Cannot render recommendations:', {
                    'has_json': !!json,
                    'json_ok': json?.ok,
                    'has_recommendations': !!(json?.recommendations),
                    'recommendations_length': json?.recommendations?.length || 0,
                    'all_keys': json ? Object.keys(json) : []
                });
                
                // ✅ JIKA TIDAK ADA REKOMENDASI, COBA FALLBACK
                if (apiFaceShape) {
                    console.log('🔄 Trying fallback loadRecs()...');
                    loadRecs().then((fallbackSuccess) => {
                        if (fallbackSuccess) {
                            console.log('✅ Fallback loadRecs() successful');
                            if (loading) loading.classList.add('hidden');
                        } else {
                            console.error('❌ Fallback loadRecs() also failed');
                            if (loading) loading.classList.add('hidden');
                            if (typeof showNotification === 'function') {
                                showNotification(
                                    'error',
                                    'Tidak Ada Rekomendasi',
                                    'Tidak ada rekomendasi yang tersedia. Silakan coba scan ulang.',
                                    6000
                                );
                            }
                        }
                    });
                } else {
                    console.error('❌ Cannot use fallback - apiFaceShape is invalid');
                    if (loading) loading.classList.add('hidden');
                }
            }
            
            // Pastikan data tersimpan - jika tidak, tampilkan notifikasi lagi
            if (json && json.recommendations && json.recommendations.length > 0) {
                if (!json.saved) {
                    if (!json.save_error) {
                        // Jika tidak ada error tapi tidak tersimpan, tampilkan warning
                        showNotification('warning', 'Peringatan!', 
                            'Rekomendasi berhasil ditampilkan, namun data tidak tersimpan ke database. Silakan refresh halaman dan coba lagi.', 7000);
                    }
                    console.error('❌ CRITICAL: Data TIDAK tersimpan ke database meskipun rekomendasi ada!');
                    if (DEBUG) {
                        console.error('❌ Save status:', json.saved);
                        console.error('❌ Save error:', json.save_error);
                        console.error('❌ Response:', json);
                    }
                } else {
                    if (DEBUG) {
                        console.log('✅✅✅ DATA BERHASIL DISIMPAN KE DATABASE! ✅✅✅');
                        console.log('✅ ID:', json.saved_id);
                    }
                }
                
                return true; // Berhasil, tidak perlu fallback
            } else {
                if (DEBUG) console.warn('⚠️ Tidak ada rekomendasi dari analyze, menggunakan fallback');
                return false; // Gagal, perlu fallback
            }
        } catch (e) {
            const errorMsg = e.message || 'Terjadi kesalahan saat memproses request';
            showNotification('error', 'Error!', 
                `Gagal memproses analisis: ${errorMsg}. Silakan coba lagi.`, 8000);
            console.error('❌ Analyze error:', e);
            if (DEBUG) {
                console.error('❌ Error details:', {
                    message: e.message,
                    stack: e.stack,
                    name: e.name
                });
            }
            loading?.classList.add('hidden');
            return false; // Error, perlu fallback
        }
    };

    // Fallback loader that hits rule-based recommendation API when analyze isn't used
    // CATATAN: loadRecs() TIDAK menyimpan data ke database, hanya mengambil rekomendasi
    const loadRecs = async () => {
        // 🔥 FIX #2: MATIKAN loadRecs() JIKA FACE INVALID
        if (!apiFaceShape) {
            console.error('🚫 loadRecs DIBLOK - apiFaceShape invalid', {
                faceShape,
                apiFaceShape,
                timestamp: new Date().toISOString()
            });
            
            if (typeof showNotification === 'function') {
                showNotification(
                    'error',
                    'Scan Tidak Valid',
                    'Data wajah tidak ditemukan. Silakan scan ulang.',
                    6000
                );
            }
            
            return false; // ⛔ STOP - jangan lanjutkan
        }
        
        if (DEBUG) {
            console.warn('⚠️ Using loadRecs() fallback - DATA TIDAK AKAN TERSIMPAN KE DATABASE!');
            console.warn('⚠️ Ini hanya untuk menampilkan rekomendasi, bukan untuk menyimpan data');
        }
        
        try {
            const injected = (window.__SCAN_ROUTES__ && window.__SCAN_ROUTES__.apiModels) ? window.__SCAN_ROUTES__.apiModels : null;
            const relative = '../api/recommendations/hair-models';
            // ❌ JANGAN pakai fallback 'Oval' - apiFaceShape sudah divalidasi tidak null
            const pickUrl = (u) => `${u}?face_shape=${encodeURIComponent(apiFaceShape)}`;

            let resp;
            let json;
            if (injected) {
                try {
                    resp = await fetch(pickUrl(injected));
                    if (!resp.ok) throw new Error(`Bad status ${resp.status}`);
                    json = await resp.json();
                } catch (e) {
                    if (DEBUG) console.warn('Injected URL failed, trying relative:', e);
                    resp = await fetch(pickUrl(relative));
                    json = await resp.json();
                }
            } else {
                resp = await fetch(pickUrl(relative));
                json = await resp.json();
            }

            const items = Array.isArray(json?.data) ? json.data : (Array.isArray(json) ? json : []);
            if (items.length) {
                render(items.map(m => ({ name: m.name, image_url: resolveAsset(m.image || m.illustration_url) })));
                if (DEBUG) console.warn('⚠️ PERINGATAN: Data user TIDAK tersimpan karena menggunakan fallback loadRecs()');
                return true;
            }
            return false;
        } catch (e) {
            console.error('Fallback loadRecs error:', e);
            if (DEBUG) console.error('Error details:', e);
            return false;
        }
    };
    
    // Panggil analyze() dan pastikan data tersimpan
    // analyze() akan menyimpan data ke database - INI WAJIB DIPANGGIL!
    // ALWAYS LOG - tidak pakai DEBUG flag
    console.log('🎯🎯🎯 ========== STARTING ANALYZE PROCESS ==========');
    console.log('📋 Available data from sessionStorage:', {
        hasDataUrl: !!dataUrl,
        hasFaceShape: !!faceShape,
        hasUserName: !!userName,
        hasUserPhone: !!userPhone,
        hasPrefLength: !!prefLength,
        hasPrefType: !!prefType,
        hasPrefCondition: !!prefCondition,
        dataUrlLength: dataUrl ? dataUrl.length : 0,
        userNameValue: userName || 'KOSONG',
        userPhoneValue: userPhone || 'KOSONG'
    });
    
    // Pastikan analyze() dipanggil - WAJIB untuk menyimpan data
    console.log('🔍 Checking analyze function...', {
        'analyze_exists': typeof analyze === 'function',
        'analyze_type': typeof analyze,
        'apiFaceShape': apiFaceShape,
        'faceShape': faceShape,
        'hasDataUrl': !!dataUrl
    });
    
    // ✅ CEK APAKAH REKOMENDASI SUDAH ADA DI LARAVEL SESSION (SUDAH DI-RENDER DI BLADE)
    // Hanya skip analyze() jika rekomendasi sudah ada (bukan hanya gambar)
    const hasLaravelRecommendations = document.querySelectorAll('#recommendations > div.rounded-2xl').length > 0;
    
    if (hasLaravelRecommendations) {
        console.log('✅ Laravel session recommendations detected - AI analysis already completed!', {
            recommendationsCount: document.querySelectorAll('#recommendations > div.rounded-2xl').length
        });
        // Hide loading karena rekomendasi sudah ada dan siap ditampilkan
        const loadingEl = document.getElementById('loadingAnalysis');
        if (loadingEl) {
            loadingEl.style.display = 'none';
        }
        // Jangan panggil analyze() lagi karena sudah selesai di backend
        return;
    }
    
    // ✅ JIKA REKOMENDASI BELUM ADA, TAMPILKAN LOADING DAN PANGGIL analyze()
    console.log('⚠️ No Laravel recommendations found, calling analyze() to get AI recommendations...');
    const loadingEl = document.getElementById('loadingAnalysis');
    if (loadingEl) {
        loadingEl.style.display = 'block';
    }
    
    // ✅ PASTIKAN analyze() SELALU DIPANGGIL (meskipun apiFaceShape null, untuk debugging)
    if (typeof analyze === 'function') {
        console.log('✅✅✅ analyze() function exists, calling it NOW...');
        console.log('📋 Data before analyze():', {
            apiFaceShape: apiFaceShape,
            faceShape: faceShape,
            hasDataUrl: !!dataUrl,
            dataUrlLength: dataUrl ? dataUrl.length : 0
        });
        // Panggil analyze() - INI WAJIB untuk menyimpan data ke database
        analyze().then((success) => {
            console.log('📊 Analyze result:', {
                success: success,
                type: typeof success
            });
            
                if (success) {
                    console.log('✅ Analyze successful, data should be saved to database');
                // ✅ HIDE LOADING JIKA BERHASIL
                if (loading) {
                    loading.classList.add('hidden');
                    console.log('✅ Loading hidden after successful analyze');
                }
                } else {
                    console.log('⚠️ Analyze failed or no recommendations, trying fallback...');
                // ✅ COBA FALLBACK JIKA ANALYZE GAGAL
                if (apiFaceShape) {
                    console.log('🔄 Trying fallback loadRecs()...');
                loadRecs().then((fallbackSuccess) => {
                        if (fallbackSuccess) {
                            console.log('✅ Fallback loadRecs() successful');
                            if (loading) loading.classList.add('hidden');
                        } else {
                            console.error('🚫 Semua fallback gagal - tidak ada rekomendasi yang bisa ditampilkan');
                            if (loading) loading.classList.add('hidden');
                            
                            if (typeof showNotification === 'function') {
                                showNotification(
                                    'error',
                                    'Tidak Ada Rekomendasi',
                                    'Tidak ada rekomendasi yang tersedia. Silakan coba scan ulang.',
                                    6000
                                );
                            }
                        }
                    });
                } else {
                    console.error('🚫 Cannot use fallback - apiFaceShape invalid');
                    if (loading) loading.classList.add('hidden');
                }
            }
        }).catch((error) => {
            console.error('❌ Analyze promise rejected:', error);
            console.error('❌ Error details:', {
                message: error.message,
                stack: error.stack,
                name: error.name
            });
            
            // ✅ HIDE LOADING JIKA ERROR
            if (loading) {
                loading.classList.add('hidden');
                console.log('✅ Loading hidden after analyze error');
            }
            
            // ✅ COBA FALLBACK JIKA ANALYZE ERROR
            if (apiFaceShape) {
                console.log('🔄 Analyze error, trying fallback loadRecs()...');
                loadRecs().then((fallbackSuccess) => {
                    if (fallbackSuccess) {
                        console.log('✅ Fallback loadRecs() successful after error');
                    } else {
                        console.error('❌ Fallback also failed');
                        if (typeof showNotification === 'function') {
                            showNotification(
                                'error',
                                'Gagal Memproses',
                                'Gagal memproses analisis. Silakan coba scan ulang.',
                                6000
                            );
                        }
                    }
                });
            } else {
                console.error('🚫 Cannot use fallback - apiFaceShape invalid');
                if (typeof showNotification === 'function') {
                    showNotification(
                        'error',
                        'Scan Tidak Valid',
                        'Data wajah tidak ditemukan. Silakan scan ulang.',
                        6000
                    );
                }
            }
        });
    } else {
        console.error('❌ CRITICAL ERROR: analyze() function not found!');
        console.error('❌ This means data will NOT be saved to database!');
        
        // ✅ HIDE LOADING JIKA FUNCTION TIDAK ADA
        if (loading) {
            loading.classList.add('hidden');
            console.log('✅ Loading hidden - analyze function not found');
        }
        
        // ✅ COBA FALLBACK JIKA ANALYZE FUNCTION TIDAK ADA
        if (apiFaceShape) {
            console.log('🔄 analyze() not found, trying fallback loadRecs()...');
            loadRecs().then((fallbackSuccess) => {
                if (fallbackSuccess) {
                    console.log('✅ Fallback loadRecs() successful');
                } else {
                    console.error('❌ Fallback also failed');
                    if (typeof showNotification === 'function') {
                        showNotification(
                            'error',
                            'Fungsi Tidak Tersedia',
                            'Fungsi analisis tidak tersedia. Silakan refresh halaman.',
                            6000
                        );
                    }
                }
            });
        } else {
            console.error('🚫 Cannot use fallback - apiFaceShape invalid');
            if (typeof showNotification === 'function') {
                showNotification(
                    'error',
                    'Scan Tidak Valid',
                    'Data wajah tidak ditemukan. Silakan scan ulang.',
                    6000
                );
            }
        }
    }
});
