# CONTEXT — Sistem Monitoring Hafalan Al-Qur'an Santri

> File ini adalah sumber kebenaran proyek. AI lain yang diminta membantu proyek ini
> WAJIB membaca file ini dulu sebelum menjawab/mengubah kode.

## 1. Ringkasan

Aplikasi web Laravel untuk mencatat & memantau hafalan Al-Qur'an santri.
Ustad input 4 jenis setoran (Sabaq, Sabqy, Qiroah, Manzil); wali santri memantau
hafalan anak secara online; admin mengelola data ustad/santri/wali santri + rekap + export PDF/Excel.
Admin hanya dapat melihat data setoran (tidak bisa input/edit/hapus setoran).
(Ejaan resmi: **ustad**, bukan ustaz — berlaku di kode, tabel DB, role, dan URL.)

## 2. Environment

- OS: Windows. Web server: **Laragon (Apache httpd-2.4.66, port 80)** + MySQL 8.4.3
  (`C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysql.exe`, user `root`, password kosong)
- PHP CLI: `C:\xampp\php\php.exe` versi 8.2.12 (`php --ini` → `C:\xampp\php\php.ini`)
- Project: `C:\laragon\www\kp` — Laravel **12.x**, Breeze (Blade + Tailwind + Vite, sudah `npm run build`)
- URL utama: **`http://kp.test/`** (Laragon). Alternatif: `php artisan serve` → `http://127.0.0.1:8000`
- DB: MySQL `monitoring_hafalan`. `.env`: `DB_CONNECTION=mysql`, `DB_HOST=127.0.0.1`,
  `DB_PORT=3306`, `DB_DATABASE=monitoring_hafalan`, `DB_USERNAME=root`, `DB_PASSWORD=` (kosong),
  `APP_URL=http://kp.test`, `APP_NAME="Monitoring Hafalan"`

### Perbaikan infra yang SUDAH dilakukan (jangan diulang/di-revert)

1. `C:\laragon\etc\apache2\sites-enabled\auto.kp.test.conf` → `DocumentRoot` dan
   `<Directory>` diganti dari `C:/laragon/www/kp` menjadi **`C:/laragon/www/kp/public`**.
   Lalu Apache di-restart. Jika suatu saat `http://kp.test/` menampilkan "Index of /",
   berarti vhost ter-reset oleh Laragon — ulangi edit ini + restart Apache.
   (File `auto.*` bisa ditulis ulang Laragon; cek isinya dulu bila error ini muncul.)
2. `C:\xampp\php\php.ini` → `;extension=gd` dibuka menjadi `extension=gd`
   (dibutuhkan PhpSpreadsheet/dompdf).
3. `composer.json` → `composer config audit.block-insecure false` + require
   `maatwebsite/excel ^3.1` + `phpoffice/phpspreadsheet ^1.29` (versi exact 3.1.66/67
   diblokir security advisory; JANGAN pin ke versi exact).
4. PowerShell stripping karakter `^` pada `composer require` — JANGAN pakai
   `composer require pkg:^x.y` di shell ini; edit `composer.json` manual lalu
   `composer update`. `npm` harus dipanggil sebagai `npm.cmd`.

## 3. Cara menjalankan

```powershell
cd C:\laragon\www\kp
# Pastikan Laragon (Apache + MySQL) status Started. Lalu buka:
#   http://kp.test/        (landing page, bukan bawaan Laravel lagi)
#   http://kp.test/login
# Alternatif tanpa Laragon:
php artisan serve  # http://127.0.0.1:8000
```

Setup dari nol (jika DB hilang): buat DB `monitoring_hafalan`, lalu
`php artisan migrate:fresh --seed`, `npm.cmd run build`.

## 4. Akun demo (password semua: `password`)

- Admin: `admin@example.com` → `/admin/dashboard`
- Ustad: `ustadahmad@example.com` (+ `ustadwarno@`, `ustadhanif@`, `ustadwage@`, `ustadbowo@example.com`) → `/ustad/dashboard`
- Orang tua: `orangtuaarka@example.com` (+ `orangtuacia@` … `orangtuazoe@example.com`) → `/orangtua/dashboard`

## 5. Role & otorisasi

- `users.role`: `admin | ustad | orang_tua` (default `orang_tua`; label tampilan: **Wali Santri**).
  Registrasi publik dinonaktifkan (route `/register` dihapus).
- Fitur lupa password aktif via Breeze (`/forgot-password`, `/reset-password/{token}`);
  `MAIL_MAILER=log` (link reset tercatat di `storage/logs/laravel.log`). Link "Lupa password?" ada di halaman login.
- Middleware `App\Http\Middleware\CheckRole`, alias `role` didaftarkan di
  `bootstrap/app.php`. Dipakai sebagai `role:admin`, `role:ustad`, `role:orang_tua`.
- Redirect login by role: `User::redirectPathByRole()` (`app/Models/User.php`),
  dipakai di `AuthenticatedSessionController@store` dan `/dashboard`.
- Ustad hanya boleh akses santri/setoran binaannya; orang tua hanya anaknya
  (scoping di `SetoranController::scopeByRole()` + `authorizeSetoran()`,
  `SantriController` memfilter by role). Admin akses penuh.

## 6. Database (MySQL)

- `users`: id, name, email, password (hash), **role**, timestamps
- `ustad`: id, **niu(unique, Nomor Induk Ustad)**, user_id→users, nama, nomor_hp, alamat, status(aktif/nonaktif)
- `orang_tua` (label tampilan: **wali santri**): id, user_id→users, nama, nomor_hp, alamat, status(aktif/nonaktif)
- `santri`: id, nama, nis(unique), jenis_kelamin(L/P), tanggal_lahir, alamat,
  orang_tua_id→orang_tua, ustad_id→ustad, tanggal_masuk, target_hafalan, status
- `setoran`: id, santri_id→santri, ustad_id→ustad, jenis_setoran
  (sabaq/sabqy/qiroah/manzil), tanggal_setoran, juz_mulai, juz_selesai,
  surat_mulai, surat_selesai, ayat_mulai, ayat_selesai, jumlah_halaman,
  jumlah_kesalahan, status (**lanjut/mengulang** saja), catatan.
  (Kolom lama `kelancaran/tajwid/makhraj/nilai` masih ada di DB tapi tidak dipakai di form/laporan.)
- Migrasi: `database/migrations/2026_09_26_000010_*` (role),
  `2026_09_26_000020_*` (4 tabel hafalan, awalnya ejaan `ustaz`),
  `2026_09_27_000030_*` (status orang_tua), `2026_09_27_000031_*`
  (rename total `ustaz`→`ustad`: tabel, kolom, FK, role). Seeder:
  `database/seeders/DatabaseSeeder.php`
  (16 users, 5 ustad, 10 ortu, 10 santri, 80 setoran).

## 7. Relasi Eloquent

`User hasOne Ustad|OrangTua`; `Ustad hasMany Santri|Setoran`;
`OrangTua hasMany Santri`; `Santri belongsTo Ustad|OrangTua, hasMany Setoran`;
`Setoran belongsTo Santri|Ustad`. Konstanta jenis: `Setoran::JENIS`.

## 8. Routes (`routes/web.php`)

- `/` (name `home`) → landing `views/landing.blade.php` untuk guest,
  redirect by role jika sudah login. (Welcome bawaan Laravel TIDAK dipakai.)
- `/dashboard` → redirect by role.
- `admin/*` (`role:admin`): `dashboard`, resource `ustad|orangtua|santri`,
  resource `setoran` hanya `index,show` (read-only), `rekap|rekap/pdf|rekap/excel`
- `ustad/*` (`role:ustad`): `dashboard`, resource `santri`(index,show,create,store),
  resource `setoran` (full), `rekap|rekap/pdf|rekap/excel`
- `orangtua/*` (`role:orang_tua`): `dashboard`, resource `santri`(index,show),
  resource `setoran`(index,show), `rekap|rekap/pdf`
- `routes/auth.php` bawaan Breeze (login/register/password reset).

## 9. Controller (`app/Http/Controllers/`)

- `DashboardController`: `admin()` (9 angka statistik, setoran/bulan 12 bln,
  count per jenis, setoran & santri terbaru), `ustad()` (count binaan,
  setoran hari/minggu ini, list santri + setoran terakhir), `orangtua()`
  (per anak: total setoran, rata nilai, setoran terakhir, juz unik;
  pakai `firstOrCreate` agar akun register mandiri tidak 404)
- `UstadController`, `OrangTuaController`: resource + search `?q`;
  auto-create `User` (role sesuai, password default `password`) saat store
  tanpa `user_id`; destroy ikut hapus user
- `SantriController`: index (search `q`, filter `ustad_id`, paginate 10,
  scoping role: ustad hanya binaan, ortu hanya anak), create+store juga
  untuk role ustad (`ustad_id` otomatis dari ustad login),
  show (progress juz unik/target_hafalan, rata nilai, riwayat)
- `SetoranController`: index (filter jenis_setoran/tanggal/santri/search +
  scoping role), validasi dinamis per `jenis_setoran`, `ustad_id` otomatis
  dari ustad login (admin bisa pilih)
- `RekapController`: `index` (filter santri_id/jenis/dari/sampai/juz +
  rekap per santri), `exportPdf` (dompdf, view `rekap/pdf.blade.php`),
  `exportExcel` (`App\Exports\RekapExport`, FromCollection+WithHeadings)

## 10. Views (`resources/views/`)

- `landing.blade.php` (homepage, x-guest-layout), `layouts/sidebar.blade.php`
  (partial `@props(['role'])`)
- `dashboard/admin|ustad|orangtua.blade.php` (card + Chart.js CDN + tabel)
- `ustad|orangtua/{index,create,edit}.blade.php`, `santri/{index,create,edit,show}.blade.php`
- `setoran/{index,create,edit,show}.blade.php` — create/edit ada tombol
  [SABAQ][SABQY][QIROAH][MANZIL] + JS toggle field per jenis; `jumlah_juz`
  disinkron ke `jumlah_halaman`
- `rekap/{index,pdf}.blade.php` (pdf: inline CSS, tanpa Tailwind CDN)
- Konvensi: `@csrf`, `@method`, `@error`+`old()`, alert session,
  route prefix dinamis per role, Tailwind + pagination bawaan.

## 11. Testing yang sudah lolos

`migrate:fresh --seed` OK, `route:list` lengkap, `view:cache` OK,
`php artisan test` PASS, `GET /` + `/login` 200 di `kp.test` maupun `:8000`.

## 12. Batasan / TODO jika diminta user

- `welcome.blade.php` masih ada tapi tidak dipakai (route `/` → `landing`).
- `orangtua/rekap/excel` belum ada (hanya pdf) — tambahkan bila diminta.
- Riwayat aktivitas (activity log) belum ada — belum diimplementasikan.
- Belum ada test otomatis khusus (hanya ExampleTest bawaan).
