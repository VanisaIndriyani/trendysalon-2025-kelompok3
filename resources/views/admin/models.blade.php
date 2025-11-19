@extends('admin.layout')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold">Kelola Data Model Rambut</h1>
                <p class="mt-1 text-sm text-stone-600">Manajemen Data Model Rambut</p>
            </div>
            <button class="inline-flex items-center gap-2 rounded-xl bg-pink-500 text-white px-4 py-2 shadow hover:bg-pink-600 transition">
                <span class="text-lg">+</span>
                <span class="font-semibold">Tambah Model</span>
            </button>
        </div>

        <!-- Search -->
        <div class="rounded-2xl ring-1 ring-stone-200 bg-white px-4 py-3 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 text-stone-500"><circle cx="11" cy="11" r="7" stroke-width="1.5"/><path d="M20 20l-3-3" stroke-width="1.5"/></svg>
            <input id="model-search" type="text" placeholder="Cari Model Rambut..." class="w-full bg-transparent outline-none text-sm" autocomplete="off" />
        </div>

        <!-- Table card -->
        <div class="rounded-2xl ring-1 ring-stone-200 bg-white overflow-hidden">
            <div class="grid grid-cols-[2fr_1fr_2fr_1fr_1fr] gap-2 px-6 py-3 text-sm font-semibold text-pink-800 bg-pink-50">
                <div>Nama Model</div>
                <div>Ilustrasi</div>
                <div>Jenis Rambut</div>
                <div>Panjang</div>
                <div>Action</div>
            </div>

            <div class="divide-y divide-stone-100">
                @forelse($models as $m)
                    <div class="grid grid-cols-[2fr_1fr_2fr_1fr_1fr] gap-2 px-6 py-4 items-center">
                        <div class="text-sm font-medium">{{ $m->name }}</div>
                        <div>
                            @if($m->image)
                                <img src="{{ asset($m->image) }}" alt="{{ $m->name }}" class="h-12 w-12 object-cover rounded-full ring-1 ring-stone-200" />
                            @else
                                <span class="text-xs text-stone-500">Tidak ada</span>
                            @endif
                        </div>
                        <div class="text-sm text-stone-700">{{ $m->types }}</div>
                        <div class="text-sm text-stone-700">{{ $m->length }}</div>
                        <div class="flex items-center gap-3 text-pink-600">
                            <button title="Edit" class="hover:text-pink-700 btn-edit" data-id="{{ $m->id }}" data-name="{{ $m->name }}" data-image="{{ $m->image }}" data-types="{{ $m->types }}" data-length="{{ $m->length }}" data-faceshapes="{{ $m->face_shapes }}">✏️</button>
                            <button title="Hapus" class="hover:text-pink-700 btn-delete" data-id="{{ $m->id }}" data-name="{{ $m->name }}">🗑️</button>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-6 text-sm text-stone-600">Belum ada data model rambut.</div>
                @endforelse
            </div>
        </div>

        <!-- Notification Success (Centered, auto-hide) -->
        @if(session('success'))
            <div id="center-notification" class="fixed inset-0 z-50">
                <div class="absolute inset-0 bg-black/30 backdrop-blur-sm"></div>
                <div class="absolute inset-0 flex items-center justify-center p-4">
                    <div class="relative z-10 w-full max-w-md rounded-2xl bg-white ring-1 ring-stone-200 shadow-xl px-6 py-5 text-center">
                        <div class="mx-auto mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-pink-100 text-pink-700 ring-1 ring-pink-200">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5"><path d="M9 12l2 2 4-4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><circle cx="12" cy="12" r="9" stroke-width="1.5"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-pink-900">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Modals -->
        <!-- Add Modal -->
        <div id="modal-add" class="fixed inset-0 bg-black/30 backdrop-blur-sm hidden z-40">
            <div class="absolute inset-0 flex items-center justify-center p-4">
                <div class="w-full max-w-lg max-h-[80vh] overflow-y-auto rounded-2xl bg-white ring-1 ring-stone-200 shadow-xl">
                    <div class="px-6 py-4 border-b">
                        <h3 class="font-bold text-lg">Tambah Model Rambut</h3>
                    </div>
                    <form method="POST" action="{{ route('admin.models.store') }}" enctype="multipart/form-data" class="px-6 py-4 space-y-4">
                        @csrf
                        <input type="hidden" name="context" value="admin-add" />
                        @if ($errors->any() && old('context')==='admin-add')
                            <div class="rounded-xl bg-red-50 text-red-700 px-3 py-2 text-xs">{{ $errors->first() }}</div>
                        @endif
                        <div>
                            <label class="text-sm font-medium">Nama Model</label>
                            <input name="name" type="text" value="{{ old('name') }}" class="mt-1 w-full rounded-xl ring-1 ring-stone-200 px-3 py-2" required />
                        </div>
                        <div>
                            <label class="text-sm font-medium">Upload Gambar (opsional)</label>
                            <input name="image_file" type="file" accept="image/*" class="mt-1 w-full rounded-xl ring-1 ring-stone-200 px-3 py-2" />
                            <p class="text-xs text-stone-500 mt-1">Format gambar, maks 4MB.</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium">Jenis Rambut</label>
                            <input name="types" type="text" value="{{ old('types') }}" placeholder="mis. Lurus, Ikal" class="mt-1 w-full rounded-xl ring-1 ring-stone-200 px-3 py-2" required />
                        </div>
                        <div>
                            <label class="text-sm font-medium">Panjang</label>
                            <select name="length" class="mt-1 w-full rounded-xl ring-1 ring-stone-200 px-3 py-2" required>
                                <option value="Pendek">Pendek</option>
                                
                                <option value="Panjang">Panjang</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm font-medium">Cocok untuk bentuk wajah</label>
                            <select name="face_shapes[]" id="add-face_shapes" multiple class="mt-1 w-full rounded-xl ring-1 ring-stone-200 px-3 py-2">
                                <option value="Oval">Oval</option>
                                <option value="Round">Round</option>
                                <option value="Square">Square</option>
                                <option value="Heart">Heart</option>
                                <option value="Oblong">Oblong</option>
                            </select>
                            <p class="text-xs text-stone-500 mt-1">Pilih satu atau lebih bentuk wajah yang direkomendasikan.</p>
                        </div>
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" class="px-4 py-2 rounded-xl ring-1 ring-stone-200 bg-stone-50 hover:bg-stone-100" data-close="#modal-add">Batal</button>
                            <button type="submit" class="px-4 py-2 rounded-xl bg-pink-600 text-white hover:bg-pink-700">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div id="modal-edit" class="fixed inset-0 bg-black/30 backdrop-blur-sm hidden z-40">
            <div class="absolute inset-0 flex items-center justify-center p-4">
                <div class="w-full max-w-lg max-h-[80vh] overflow-y-auto rounded-2xl bg-white ring-1 ring-stone-200 shadow-xl">
                    <div class="px-6 py-4 border-b">
                        <h3 class="font-bold text-lg">Edit Model Rambut</h3>
                    </div>
                    <form method="POST" action="#" id="form-edit" enctype="multipart/form-data" class="px-6 py-4 space-y-4">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="context" value="admin-edit" />
                        <input type="hidden" name="id" id="edit-id" value="{{ old('id') }}" />
                        @if ($errors->any() && old('context')==='admin-edit')
                            <div class="rounded-xl bg-red-50 text-red-700 px-3 py-2 text-xs">{{ $errors->first() }}</div>
                        @endif
                        <div>
                            <label class="text-sm font-medium">Nama Model</label>
                            <input name="name" id="edit-name" type="text" value="{{ old('name') }}" class="mt-1 w-full rounded-xl ring-1 ring-stone-200 px-3 py-2" required />
                        </div>
                        <div>
                            <label class="text-sm font-medium">Upload Gambar (opsional)</label>
                            <input name="image_file" id="edit-image_file" type="file" accept="image/*" class="mt-1 w-full rounded-xl ring-1 ring-stone-200 px-3 py-2" />
                            <div class="mt-2 flex items-center gap-3">
                                <div class="h-14 w-14 rounded-full overflow-hidden ring-1 ring-stone-200 bg-stone-50">
                                    <img id="edit-preview" src="" alt="Preview" class="h-full w-full object-cover" />
                                </div>
                                <p class="text-xs text-stone-600">Preview gambar saat ini / pilihan baru.</p>
                            </div>
                            <p class="text-xs text-stone-500 mt-1">Kosongkan jika tidak mengganti gambar. Maks 4MB.</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium">Jenis Rambut</label>
                            <input name="types" id="edit-types" type="text" value="{{ old('types') }}" class="mt-1 w-full rounded-xl ring-1 ring-stone-200 px-3 py-2" required />
                        </div>
                        <div>
                            <label class="text-sm font-medium">Panjang</label>
                            <select name="length" id="edit-length" class="mt-1 w-full rounded-xl ring-1 ring-stone-200 px-3 py-2" required>
                                <option value="Pendek">Pendek</option>
                           
                                <option value="Panjang">Panjang</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-sm font-medium">Cocok untuk bentuk wajah</label>
                            <select name="face_shapes[]" id="edit-face_shapes" multiple class="mt-1 w-full rounded-xl ring-1 ring-stone-200 px-3 py-2">
                                <option value="Oval">Oval</option>
                                <option value="Round">Round</option>
                                <option value="Square">Square</option>
                                <option value="Heart">Heart</option>
                                <option value="Oblong">Oblong</option>
                            </select>
                            <p class="text-xs text-stone-500 mt-1">Pilih satu atau lebih bentuk wajah yang direkomendasikan.</p>
                        </div>
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" class="px-4 py-2 rounded-xl ring-1 ring-stone-200 bg-stone-50 hover:bg-stone-100" data-close="#modal-edit">Batal</button>
                            <button type="submit" class="px-4 py-2 rounded-xl bg-pink-600 text-white hover:bg-pink-700">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div id="modal-delete" class="fixed inset-0 bg-black/30 backdrop-blur-sm hidden z-40">
            <div class="absolute inset-0 flex items-center justify-center p-4">
                <div class="w-full max-w-md max-h-[80vh] overflow-y-auto rounded-2xl bg-white ring-1 ring-stone-200 shadow-xl">
                    <div class="px-6 py-4 border-b">
                        <h3 class="font-bold text-lg">Hapus Model</h3>
                    </div>
                    <form method="POST" action="#" id="form-delete" class="px-6 py-4 space-y-4">
                        @csrf
                        @method('DELETE')
                        <p class="text-sm">Apakah Anda yakin ingin menghapus <span id="delete-name" class="font-semibold"></span>?</p>
                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" class="px-4 py-2 rounded-xl ring-1 ring-stone-200 bg-stone-50 hover:bg-stone-100" data-close="#modal-delete">Batal</button>
                            <button type="submit" class="px-4 py-2 rounded-xl bg-pink-600 text-white hover:bg-pink-700">Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Script -->
        <script>
            // Open Add Modal
            document.addEventListener('DOMContentLoaded', function () {
                const btnAdd = document.querySelector('button.inline-flex.items-center');
                const modalAdd = document.getElementById('modal-add');
                const modalEdit = document.getElementById('modal-edit');
                const modalDelete = document.getElementById('modal-delete');

                function openModal(el) { el.classList.remove('hidden'); }
                function closeModal(el) { el.classList.add('hidden'); }

                if (btnAdd) btnAdd.addEventListener('click', () => openModal(modalAdd));

                document.querySelectorAll('[data-close]')?.forEach(btn => {
                    btn.addEventListener('click', () => {
                        const target = document.querySelector(btn.getAttribute('data-close'));
                        if (target) closeModal(target);
                    });
                });

                // Edit buttons
                document.querySelectorAll('.btn-edit').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const id = btn.getAttribute('data-id');
                        document.getElementById('edit-name').value = btn.getAttribute('data-name') || '';
                        document.getElementById('edit-types').value = btn.getAttribute('data-types') || '';
                        document.getElementById('edit-length').value = btn.getAttribute('data-length') || 'Panjang';
                        document.getElementById('form-edit').setAttribute('action', `{{ url('/admin/models') }}/${id}`);
                        const hiddenId = document.getElementById('edit-id');
                        if (hiddenId) hiddenId.value = id;
                        // Preselect face shapes
                        const fsSel = document.getElementById('edit-face_shapes');
                        if (fsSel) {
                            const fsStr = btn.getAttribute('data-faceshapes') || '';
                            const vals = fsStr ? fsStr.split(',').map(s => s.trim()) : [];
                            Array.from(fsSel.options).forEach(opt => { opt.selected = vals.includes(opt.value); });
                        }
                        // Set current image preview
                        const imgRel = btn.getAttribute('data-image') || '';
                        const prevEl = document.getElementById('edit-preview');
                        if (prevEl) {
                            const base = window.location.origin.replace(/\/$/, '');
                            const src = imgRel ? (imgRel.startsWith('http') ? imgRel : `${base}/${imgRel}`) : '';
                            prevEl.src = src;
                        }
                        // Clear file input on open
                        const fileInput = document.getElementById('edit-image_file');
                        if (fileInput) fileInput.value = '';
                        openModal(modalEdit);
                    });
                });

                // Live preview on file selection in edit modal
                const fileInputEdit = document.getElementById('edit-image_file');
                if (fileInputEdit) {
                    fileInputEdit.addEventListener('change', () => {
                        const f = fileInputEdit.files?.[0];
                        if (!f) return;
                        const url = URL.createObjectURL(f);
                        const prevEl = document.getElementById('edit-preview');
                        if (prevEl) prevEl.src = url;
                    });
                }

                // Delete buttons
                document.querySelectorAll('.btn-delete').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const id = btn.getAttribute('data-id');
                        const name = btn.getAttribute('data-name');
                        document.getElementById('delete-name').textContent = name || '';
                        document.getElementById('form-delete').setAttribute('action', `{{ url('/admin/models') }}/${id}`);
                        openModal(modalDelete);
                    });
                });

                // Auto hide centered notification
                const notif = document.getElementById('center-notification');
                if (notif) setTimeout(() => notif.classList.add('hidden'), 2500);

                // Live search filter for models list
                const input = document.getElementById('model-search');
                const list = document.querySelector('.divide-y');
                const rows = list ? Array.from(list.querySelectorAll('.grid')) : [];
                let emptyMsg = null;
                const ensureEmpty = () => {
                    if (!list) return; 
                    if (!emptyMsg) {
                        emptyMsg = document.createElement('div');
                        emptyMsg.className = 'px-6 py-6 text-sm text-stone-600';
                        emptyMsg.textContent = 'Tidak ada hasil.';
                        emptyMsg.style.display = 'none';
                        list.appendChild(emptyMsg);
                    }
                };
                ensureEmpty();
                const apply = () => {
                    const q = (input?.value || '').toLowerCase();
                    let visible = 0;
                    rows.forEach(r => {
                        const text = r.textContent?.toLowerCase() || '';
                        const match = !q || text.includes(q);
                        r.style.display = match ? '' : 'none';
                        if (match) visible++;
                    });
                    if (emptyMsg) emptyMsg.style.display = visible === 0 ? '' : 'none';
                };
                input?.addEventListener('input', apply);
                apply();

                // Auto-open modal when validation fails
                try {
                    const hadErrors = JSON.parse('{{ $errors->any() ? 'true' : 'false' }}');
                    const ctx = '{{ old('context') }}';
                    const oldId = '{{ old('id') }}';
                    if (hadErrors && ctx === 'admin-edit') {
            if (oldId) document.getElementById('form-edit')?.setAttribute('action', `{{ url('/admin/models') }}/${oldId}`);
                        openModal(modalEdit);
                    } else if (hadErrors && ctx === 'admin-add') {
                        openModal(modalAdd);
                    }
                } catch {}
            });
        </script>
    </div>
@endsection