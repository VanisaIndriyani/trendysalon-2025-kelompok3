@extends('super.layout')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold">Kelola Data Vitamin Rambut</h1>
                <p class="mt-1 text-sm text-stone-600">Manajemen Data Vitamin Rambut</p>
            </div>
            <button id="btn-open-add" class="inline-flex items-center gap-2 rounded-xl bg-pink-500 text-white px-4 py-2 shadow hover:bg-pink-600 transition">
                <span class="text-lg">+</span>
                <span class="font-semibold">Tambah Vitamin</span>
            </button>
        </div>

        <!-- Search -->
        <div class="rounded-2xl ring-1 ring-stone-200 bg-white px-4 py-3 flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="h-5 w-5 text-stone-500"><circle cx="11" cy="11" r="7" stroke-width="1.5"/><path d="M20 20l-3-3" stroke-width="1.5"/></svg>
            <input id="vitamin-search" type="text" placeholder="Cari Vitamin Rambut..." class="w-full bg-transparent outline-none text-sm" autocomplete="off" />
        </div>

        <!-- Table card -->
        <div class="rounded-2xl ring-1 ring-stone-200 bg-white overflow-hidden">
            <table class="w-full text-sm table-auto">
                <thead class="bg-pink-50 text-pink-800">
                    <tr>
                        <th class="w-[45%] px-6 py-3 text-left font-semibold align-middle">Nama Vitamin</th>
                        <th class="w-[35%] px-6 py-3 text-left font-semibold align-middle">Tipe Rambut</th>
                        <th class="w-[20%] px-6 py-3 text-right font-semibold align-middle">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                @forelse($vitamins as $v)
                    <tr>
                        <td class="w-[45%] px-6 py-4 font-medium align-middle">{{ $v->name }}</td>
                        <td class="w-[35%] px-6 py-4 text-stone-700 align-middle">{{ $v->hair_type }}</td>
                        <td class="w-[20%] px-6 py-4 text-right align-middle">
                            <div class="inline-flex items-center gap-3 text-pink-600 justify-end">
                                <button title="Edit" class="hover:text-pink-700 btn-edit" data-id="{{ $v->id }}" data-name="{{ $v->name }}" data-hair_type="{{ $v->hair_type }}">✏️</button>
                                <button title="Hapus" class="hover:text-pink-700 btn-delete" data-id="{{ $v->id }}" data-name="{{ $v->name }}">🗑️</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr data-empty="true">
                        <td colspan="3" class="px-6 py-6 text-stone-600">Belum ada data vitamin rambut.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- Center Notification Modal -->
        @if(session('success'))
            <div id="center-notif" class="fixed inset-0 z-50 grid place-items-center">
                <div class="absolute inset-0 bg-black/30"></div>
                <div class="relative z-10 bg-white rounded-2xl ring-1 ring-pink-200 p-6 shadow-xl flex items-center gap-3">
                    <div class="h-8 w-8 rounded-full grid place-items-center bg-pink-50 ring-1 ring-pink-200 text-pink-600">✓</div>
                    <div>
                        <p class="font-semibold text-stone-900">Berhasil</p>
                        <p class="text-sm text-stone-600">{{ session('success') }}</p>
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
                        <h3 class="font-bold text-lg">Tambah Vitamin Rambut</h3>
                    </div>
                    <form method="POST" action="{{ route('super.vitamins.store') }}" class="px-6 py-4 space-y-4">
                        @csrf
                        <div>
                            <label class="text-sm font-medium">Nama Vitamin</label>
                            <input name="name" type="text" class="mt-1 w-full rounded-xl ring-1 ring-stone-200 px-3 py-2" required />
                        </div>
                        <div>
                            <label class="text-sm font-medium">Tipe Rambut</label>
                            <select name="hair_type" class="mt-1 w-full rounded-xl ring-1 ring-stone-200 px-3 py-2" required>
                                <option value="Sehat">Sehat</option>
                                <option value="Kering">Kering</option>
                                <option value="Rusak">Rusak</option>
                            </select>
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
                        <h3 class="font-bold text-lg">Edit Vitamin Rambut</h3>
                    </div>
                    <form method="POST" action="#" id="form-edit" class="px-6 py-4 space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="text-sm font-medium">Nama Vitamin</label>
                            <input name="name" id="edit-name" type="text" class="mt-1 w-full rounded-xl ring-1 ring-stone-200 px-3 py-2" required />
                        </div>
                        <div>
                            <label class="text-sm font-medium">Tipe Rambut</label>
                            <select name="hair_type" id="edit-hair_type" class="mt-1 w-full rounded-xl ring-1 ring-stone-200 px-3 py-2" required>
                                <option value="Sehat">Sehat</option>
                                <option value="Kering">Kering</option>
                                <option value="Rusak">Rusak</option>
                            </select>
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
                        <h3 class="font-bold text-lg">Hapus Vitamin</h3>
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
            document.addEventListener('DOMContentLoaded', function () {
                const modalAdd = document.getElementById('modal-add');
                const modalEdit = document.getElementById('modal-edit');
                const modalDelete = document.getElementById('modal-delete');

                document.getElementById('btn-open-add')?.addEventListener('click', () => modalAdd.classList.remove('hidden'));
                document.querySelectorAll('[data-close]')?.forEach(btn => {
                    btn.addEventListener('click', () => {
                        const target = document.querySelector(btn.getAttribute('data-close'));
                        if (target) target.classList.add('hidden');
                    });
                });

                document.querySelectorAll('.btn-edit').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const id = btn.getAttribute('data-id');
                        document.getElementById('edit-name').value = btn.getAttribute('data-name') || '';
                        document.getElementById('edit-hair_type').value = btn.getAttribute('data-hair_type') || 'Sehat';
                        document.getElementById('form-edit').setAttribute('action', `{{ url('/super/vitamins') }}/${id}`);
                        modalEdit.classList.remove('hidden');
                    });
                });

                document.querySelectorAll('.btn-delete').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const id = btn.getAttribute('data-id');
                        const name = btn.getAttribute('data-name');
                        document.getElementById('delete-name').textContent = name || '';
                        document.getElementById('form-delete').setAttribute('action', `{{ url('/super/vitamins') }}/${id}`);
                        modalDelete.classList.remove('hidden');
                    });
                });

                const centerNotif = document.getElementById('center-notif');
                if (centerNotif) {
                    setTimeout(() => centerNotif.classList.add('hidden'), 2500);
                }

                // Live search filter
                const searchInput = document.getElementById('vitamin-search');
                const tbody = document.querySelector('table tbody');
                const allRows = tbody ? Array.from(tbody.querySelectorAll('tr')) : [];
                const emptyRow = tbody ? tbody.querySelector('tr[data-empty]') : null;

                const applyFilter = () => {
                    const q = (searchInput?.value || '').trim().toLowerCase();
                    let visible = 0;
                    allRows.forEach(tr => {
                        if (tr.hasAttribute('data-empty')) return; // skip empty message row
                        const text = tr.textContent?.toLowerCase() || '';
                        const match = q === '' || text.includes(q);
                        tr.style.display = match ? '' : 'none';
                        if (match) visible++;
                    });
                    if (emptyRow) emptyRow.style.display = visible === 0 ? '' : 'none';
                };
                searchInput?.addEventListener('input', applyFilter);
                applyFilter();
            });
        </script>
    </div>
@endsection