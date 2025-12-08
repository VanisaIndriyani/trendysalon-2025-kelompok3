<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserHomeController;
use App\Http\Controllers\ScanController;
use Illuminate\Http\Request;
use App\Models\HairModel;
use App\Models\HairVitamin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ReportsExportController;

// Halaman User Home
Route::get('/', [UserHomeController::class, 'index'])->name('user.home');
// Halaman Admin Login
Route::get('/admin/login', function () {
    return view('admin.login');
})->name('admin.login');
// Halaman Super Admin Login diarahkan ke Admin Login (satu halaman)
Route::get('/super/login', function () {
    return redirect()->route('admin.login');
})->name('super.login');
// Submit Login (satu halaman untuk Admin & Super Admin)
Route::post('/admin/login', function (Request $request) {
    $data = $request->validate([
        'email' => ['required','email'],
        'password' => ['required','string'],
    ]);
    $user = App\Models\User::where('email', $data['email'])->first();
    if ($user && Illuminate\Support\Facades\Hash::check($data['password'], $user->password)) {
        // Bersihkan sesi role lain
        session()->forget(['admin_logged_in','admin_user_id','super_logged_in','super_user_id']);
        if ($user->role === 'super') {
            session(['super_logged_in' => true, 'super_user_id' => $user->id]);
            return redirect()->route('super.dashboard');
        }
        if ($user->role === 'admin') {
            session(['admin_logged_in' => true, 'admin_user_id' => $user->id]);
            return redirect()->route('admin.dashboard');
        }
    }
    return redirect()->route('admin.login')->with('error', 'Email atau password salah');
})->name('admin.login.submit');
// Logout Admin
Route::get('/admin/logout', function () {
    session()->forget(['admin_logged_in','admin_user_id']);
    return redirect()->route('admin.login');
})->name('admin.logout');
// Dashboard Admin
Route::get('/admin/dashboard', function () {
    if (!session('admin_logged_in')) return redirect()->route('admin.login');
    // Ambil data dashboard dari database
    $totalModels = HairModel::count();
    $totalVitamins = HairVitamin::count();
    $topVitamin = HairVitamin::query()->orderBy('name')->first();
    return view('admin.dashboard', compact('totalModels','totalVitamins','topVitamin'));
})->name('admin.dashboard');

// ============= Admin Profile =============
Route::get('/admin/profile', function () {
    if (!session('admin_logged_in')) return redirect()->route('admin.login');
    return view('admin.profile');
})->name('admin.profile');
// Upload Foto Profil Admin
Route::post('/admin/profile/photo', function (Request $request) {
    if (!session('admin_logged_in')) return redirect()->route('admin.login');
    $request->validate([
        'avatar' => ['required','image','max:3072'],
    ]);
    $uid = session('admin_user_id');
    $path = $request->file('avatar')->storeAs('avatars', 'user_'.$uid.'.jpg', 'public');
    return redirect()->route('admin.profile')->with('success', 'Foto profil berhasil diperbarui');
})->name('admin.profile.photo');
// Ubah Password Admin
Route::post('/admin/profile/password', function (Request $request) {
    if (!session('admin_logged_in')) return redirect()->route('admin.login');
    $data = $request->validate([
        'password' => ['required','string','min:8','confirmed'],
    ]);
    $uid = session('admin_user_id');
    $user = User::find($uid);
    if (!$user) return redirect()->route('admin.profile')->with('error', 'User tidak ditemukan');
    $user->update(['password' => Hash::make($data['password'])]);
    return redirect()->route('admin.profile')->with('success', 'Password berhasil diperbarui');
})->name('admin.profile.password');

// ================= Super Admin =================
// Logout Super Admin -> arahkan ke halaman login admin
Route::get('/super/logout', function () {
    session()->forget(['super_logged_in','super_user_id']);
    return redirect()->route('admin.login');
})->name('super.logout');
// Dashboard Super Admin (protected)
Route::get('/super/dashboard', function () {
    if (!session('super_logged_in')) return redirect()->route('super.login');
    return view('super.dashboard');
})->name('super.dashboard');
// ============= Super Admin Profile =============
Route::get('/super/profile', function () {
    if (!session('super_logged_in')) return redirect()->route('super.login');
    return view('super.profile');
})->name('super.profile');
// Upload Foto Profil Super Admin
Route::post('/super/profile/photo', function (Request $request) {
    if (!session('super_logged_in')) return redirect()->route('super.login');
    $request->validate([
        'avatar' => ['required','image','max:3072'],
    ]);
    $uid = session('super_user_id');
    $path = $request->file('avatar')->storeAs('avatars', 'user_'.$uid.'.jpg', 'public');
    return redirect()->route('super.profile')->with('success', 'Foto profil berhasil diperbarui');
})->name('super.profile.photo');
// Ubah Password Super Admin
Route::post('/super/profile/password', function (Request $request) {
    if (!session('super_logged_in')) return redirect()->route('super.login');
    $data = $request->validate([
        'password' => ['required','string','min:8','confirmed'],
    ]);
    $uid = session('super_user_id');
    $user = User::find($uid);
    if (!$user) return redirect()->route('super.profile')->with('error', 'User tidak ditemukan');
    $user->update(['password' => Hash::make($data['password'])]);
    return redirect()->route('super.profile')->with('success', 'Password berhasil diperbarui');
})->name('super.profile.password');
// Super Admin Reports (protected)
Route::get('/super/reports', function () {
    if (!session('super_logged_in')) return redirect()->route('super.login');
    $recs = App\Models\Recommendation::query()->orderByDesc('created_at')->get();
    return view('super.reports', compact('recs'));
})->name('super.reports');
// Export Super Reports
Route::get('/super/reports/export/excel', [ReportsExportController::class, 'excel'])->name('super.reports.export.excel');
Route::get('/super/reports/export/pdf', [ReportsExportController::class, 'pdf'])->name('super.reports.export.pdf');
// Kelola Data Model Rambut oleh Super Admin
Route::get('/super/models', function () {
    if (!session('super_logged_in')) return redirect()->route('super.login');
    $models = HairModel::query()->orderBy('name')->get();
    return view('super.models', compact('models'));
})->name('super.models');
Route::post('/super/models', function (Request $request) {
    if (!session('super_logged_in')) return redirect()->route('super.login');
    $data = $request->validate([
        'name' => ['required','string','max:255'],
        'types' => ['required','string','max:255'],
        'length' => ['required','string','max:50'],
        'image_file' => ['nullable','image','max:4096'],
        'face_shapes' => ['nullable','array'],
        'face_shapes.*' => ['in:Oval,Round,Square,Heart,Oblong'],
    ]);
    // Normalize face shapes
    $fs = $request->input('face_shapes', []);
    $data['face_shapes'] = is_array($fs) ? implode(',', $fs) : (string) $fs;
    if ($request->hasFile('image_file')) {
        $stored = $request->file('image_file')->store('models','public');
        $data['image'] = 'storage/'.$stored;
    }
    HairModel::create($data);
    return redirect()->route('super.models')->with('success', 'Model berhasil ditambahkan');
})->name('super.models.store');
Route::put('/super/models/{hairModel}', function (Request $request, HairModel $hairModel) {
    if (!session('super_logged_in')) return redirect()->route('super.login');
    $data = $request->validate([
        'name' => ['required','string','max:255'],
        'types' => ['required','string','max:255'],
        'length' => ['required','string','max:50'],
        'image_file' => ['nullable','image','max:4096'],
        'face_shapes' => ['nullable','array'],
        'face_shapes.*' => ['in:Oval,Round,Square,Heart,Oblong'],
    ]);
    // Normalize face shapes
    $fs = $request->input('face_shapes', []);
    $data['face_shapes'] = is_array($fs) ? implode(',', $fs) : (string) $fs;
    if ($request->hasFile('image_file')) {
        $stored = $request->file('image_file')->store('models','public');
        $data['image'] = 'storage/'.$stored;
    }
    $hairModel->update($data);
    return redirect()->route('super.models')->with('success', 'Model berhasil diperbarui');
})->name('super.models.update');
Route::delete('/super/models/{hairModel}', function (HairModel $hairModel) {
    if (!session('super_logged_in')) return redirect()->route('super.login');
    $name = $hairModel->name;
    $hairModel->delete();
    return redirect()->route('super.models')->with('success', "Model '$name' berhasil dihapus");
})->name('super.models.destroy');
// Kelola Data Vitamin Rambut oleh Super Admin
Route::get('/super/vitamins', function () {
    if (!session('super_logged_in')) return redirect()->route('super.login');
    $vitamins = HairVitamin::query()->orderBy('name')->get();
    return view('super.vitamins', compact('vitamins'));
})->name('super.vitamins');
Route::post('/super/vitamins', function (Request $request) {
    if (!session('super_logged_in')) return redirect()->route('super.login');
    $data = $request->validate([
        'name' => ['required','string','max:255'],
        'hair_type' => ['required','string','max:100'],
    ]);
    HairVitamin::create($data);
    return redirect()->route('super.vitamins')->with('success', 'Vitamin berhasil ditambahkan');
})->name('super.vitamins.store');
Route::put('/super/vitamins/{hairVitamin}', function (Request $request, HairVitamin $hairVitamin) {
    if (!session('super_logged_in')) return redirect()->route('super.login');
    $data = $request->validate([
        'name' => ['required','string','max:255'],
        'hair_type' => ['required','string','max:100'],
    ]);
    $hairVitamin->update($data);
    return redirect()->route('super.vitamins')->with('success', 'Vitamin berhasil diperbarui');
})->name('super.vitamins.update');
Route::delete('/super/vitamins/{hairVitamin}', function (HairVitamin $hairVitamin) {
    if (!session('super_logged_in')) return redirect()->route('super.login');
    $name = $hairVitamin->name;
    $hairVitamin->delete();
    return redirect()->route('super.vitamins')->with('success', "Vitamin '$name' berhasil dihapus");
})->name('super.vitamins.destroy');
// Manajemen Admin (protected)
Route::get('/super/admins', function () {
    if (!session('super_logged_in')) return redirect()->route('super.login');
    $admins = User::where('role','admin')->orderBy('name')->get();
    return view('super.admins', compact('admins'));
})->name('super.admins');
// Tambah Admin (protected)
Route::post('/super/admins', function (Request $request) {
    if (!session('super_logged_in')) return redirect()->route('super.login');
    $data = $request->validate([
        'name' => ['required','string','max:255'],
        'email' => ['required','email','max:255','unique:users,email'],
        'password' => ['required','string','min:8'],
    ]);
    User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
        'role' => 'admin',
    ]);
    return redirect()->route('super.admins')->with('success', 'Akun admin berhasil dibuat');
})->name('super.admins.store');
// Reset Password Admin (protected, auto ke default)
Route::post('/super/admins/{user}/reset', function (User $user) {
    if (!session('super_logged_in')) return redirect()->route('super.login');
    if ($user->role !== 'admin') return redirect()->route('super.admins')->with('error', 'Hanya admin yang bisa direset');
    $newPassword = Str::random(12);
    $user->update(['password' => Hash::make($newPassword)]);
    return redirect()->route('super.admins')->with('success', "Password baru untuk '{$user->email}' adalah: ${newPassword}");
})->name('super.admins.reset');

// Hapus Akun Admin (protected)
Route::post('/super/admins/{user}/delete', function (User $user) {
    if (!session('super_logged_in')) return redirect()->route('super.login');
    if ($user->role !== 'admin') return redirect()->route('super.admins')->with('error', 'Hanya akun admin yang dapat dihapus');
    // Hapus avatar jika ada
    $avatarRel = 'avatars/user_'.$user->id.'.jpg';
    if (Storage::disk('public')->exists($avatarRel)) {
        Storage::disk('public')->delete($avatarRel);
    }
    $email = $user->email;
    $user->delete();
    return redirect()->route('super.admins')->with('success', "Akun admin '{$email}' berhasil dihapus");
})->name('super.admins.destroy');

// Halaman Analitik & Laporan
Route::get('/admin/reports', function () {
    if (!session('admin_logged_in')) return redirect()->route('admin.login');
    $recs = App\Models\Recommendation::query()->orderByDesc('created_at')->get();
    
    // Hitung statistik vitamin berdasarkan hair_condition
    $vitaminStats = [];
    $vitamins = App\Models\HairVitamin::all();
    
    foreach ($vitamins as $vitamin) {
        // Hitung berapa kali vitamin ini direkomendasikan
        // berdasarkan match hair_condition dengan hair_type (case-insensitive + trim)
        $hairType = trim($vitamin->hair_type);
        $count = App\Models\Recommendation::whereRaw('LOWER(TRIM(hair_condition)) = ?', [strtolower($hairType)])
            ->count();
        
        $vitaminStats[] = [
            'name' => $vitamin->name,
            'count' => $count,
            'hair_type' => $vitamin->hair_type,
        ];
    }
    
    // Urutkan dari yang paling banyak direkomendasikan
    usort($vitaminStats, function($a, $b) {
        return $b['count'] - $a['count'];
    });
    
    // Ambil TOP 5 vitamin terpopuler
    $topVitamins = array_slice($vitaminStats, 0, 5);
    
    // Hitung model rambut terpopuler
    $modelCounts = [];
    foreach ($recs as $rec) {
        if ($rec->recommended_models) {
            $models = explode(',', $rec->recommended_models);
            foreach ($models as $model) {
                $model = trim($model);
                if (!empty($model)) {
                    $modelCounts[$model] = ($modelCounts[$model] ?? 0) + 1;
                }
            }
        }
    }
    arsort($modelCounts);
    $topModel = !empty($modelCounts) ? array_key_first($modelCounts) : 'Oval Layer With Curtain Bangs';
    $topVitamin = !empty($topVitamins) ? $topVitamins[0]['name'] : 'Vitamin A';
    
    return view('admin.reports', compact('recs', 'topVitamins', 'topModel', 'topVitamin', 'vitaminStats'));
})->name('admin.reports');
// Export Admin Reports
Route::get('/admin/reports/export/excel', [ReportsExportController::class, 'excel'])->name('admin.reports.export.excel');
Route::get('/admin/reports/export/pdf', [ReportsExportController::class, 'pdf'])->name('admin.reports.export.pdf');

// Kelola Data Model Rambut
Route::get('/admin/models', function () {
    if (!session('admin_logged_in')) return redirect()->route('admin.login');
    $models = HairModel::query()->orderBy('name')->get();
    return view('admin.models', compact('models'));
})->name('admin.models');

// CRUD HairModel via modal forms
Route::post('/admin/models', function (Request $request) {
    if (!session('admin_logged_in')) return redirect()->route('admin.login');
    $data = $request->validate([
        'name' => ['required','string','max:255'],
        'types' => ['required','string','max:255'],
        'length' => ['required','string','max:50'],
        'image_file' => ['nullable','image','max:4096'],
        'face_shapes' => ['nullable','array'],
        'face_shapes.*' => ['in:Oval,Round,Square,Heart,Oblong'],
    ]);
    // Normalize face shapes
    $fs = $request->input('face_shapes', []);
    $data['face_shapes'] = is_array($fs) ? implode(',', $fs) : (string) $fs;
    if ($request->hasFile('image_file')) {
        $stored = $request->file('image_file')->store('models','public');
        $data['image'] = 'storage/'.$stored; // e.g. storage/models/xxx.png
    }
    HairModel::create($data);
    return redirect()->route('admin.models')->with('success', 'Model berhasil ditambahkan');
})->name('admin.models.store');

Route::put('/admin/models/{hairModel}', function (Request $request, HairModel $hairModel) {
    if (!session('admin_logged_in')) return redirect()->route('admin.login');
    $data = $request->validate([
        'name' => ['required','string','max:255'],
        'types' => ['required','string','max:255'],
        'length' => ['required','string','max:50'],
        'image_file' => ['nullable','image','max:4096'],
        'face_shapes' => ['nullable','array'],
        'face_shapes.*' => ['in:Oval,Round,Square,Heart,Oblong'],
    ]);
    // Normalize face shapes
    $fs = $request->input('face_shapes', []);
    $data['face_shapes'] = is_array($fs) ? implode(',', $fs) : (string) $fs;
    if ($request->hasFile('image_file')) {
        $stored = $request->file('image_file')->store('models','public');
        $data['image'] = 'storage/'.$stored;
    }
    $hairModel->update($data);
    return redirect()->route('admin.models')->with('success', 'Model berhasil diperbarui');
})->name('admin.models.update');

// API rekomendasi berdasarkan bentuk wajah (simple rule-based)
Route::get('/api/recommendations/hair-models', function (Request $request) {
    $data = $request->validate([
        'face_shape' => ['required','string','in:Oval,Round,Square,Heart,Oblong'],
    ]);
    $shape = $data['face_shape'];
    $models = HairModel::query()
        ->whereNotNull('face_shapes')
        ->where('face_shapes', 'like', "%$shape%")
        ->orderBy('name')
        ->get(['id','name','image','types','length','face_shapes']);

    // Fallback: jika tidak ada satupun model yang cocok (mis. kolom face_shapes kosong di hosting),
    // tampilkan beberapa model teratas agar pengguna tetap melihat rekomendasi.
    if ($models->isEmpty()) {
        $models = HairModel::query()
            ->orderBy('name')
            ->take(4)
            ->get(['id','name','image','types','length','face_shapes']);
    }
    return response()->json(['data' => $models]);
})->name('api.recommendations.hair_models');

Route::delete('/admin/models/{hairModel}', function (HairModel $hairModel) {
    if (!session('admin_logged_in')) return redirect()->route('admin.login');
    $name = $hairModel->name;
    $hairModel->delete();
    return redirect()->route('admin.models')->with('success', "Model '$name' berhasil dihapus");
})->name('admin.models.destroy');

// Kelola Data Vitamin Rambut
Route::get('/admin/vitamins', function () {
    if (!session('admin_logged_in')) return redirect()->route('admin.login');
    $vitamins = HairVitamin::query()->orderBy('name')->get();
    return view('admin.vitamins', compact('vitamins'));
})->name('admin.vitamins');

// CRUD HairVitamin via modal forms
Route::post('/admin/vitamins', function (Request $request) {
    if (!session('admin_logged_in')) return redirect()->route('admin.login');
    $data = $request->validate([
        'name' => ['required','string','max:255'],
        'hair_type' => ['required','string','max:100'],
    ]);
    HairVitamin::create($data);
    return redirect()->route('admin.vitamins')->with('success', 'Vitamin berhasil ditambahkan');
})->name('admin.vitamins.store');

Route::put('/admin/vitamins/{hairVitamin}', function (Request $request, HairVitamin $hairVitamin) {
    if (!session('admin_logged_in')) return redirect()->route('admin.login');
    $data = $request->validate([
        'name' => ['required','string','max:255'],
        'hair_type' => ['required','string','max:100'],
    ]);
    $hairVitamin->update($data);
    return redirect()->route('admin.vitamins')->with('success', 'Vitamin berhasil diperbarui');
})->name('admin.vitamins.update');

Route::delete('/admin/vitamins/{hairVitamin}', function (HairVitamin $hairVitamin) {
    if (!session('admin_logged_in')) return redirect()->route('admin.login');
    $name = $hairVitamin->name;
    $hairVitamin->delete();
    return redirect()->route('admin.vitamins')->with('success', "Vitamin '$name' berhasil dihapus");
})->name('admin.vitamins.destroy');
// Halaman Scan Terpisah
Route::get('/scan', [UserHomeController::class, 'scan'])->name('scan');
// Halaman Kamera
Route::get('/scan/camera', function () {
    return view('user.scan_camera');
})->name('scan.camera');
// Halaman Hasil
Route::get('/scan/results', function () {
    return view('user.scan_result');
})->name('scan.results');

// Endpoint analisis AI (terima base64 image, kembalikan rekomendasi)
Route::post('/scan/analyze', [ScanController::class, 'analyze'])->name('scan.analyze');
