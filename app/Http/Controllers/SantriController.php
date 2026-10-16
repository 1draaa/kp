<?php

namespace App\Http\Controllers;

use App\Models\OrangTua;
use App\Models\Santri;
use App\Models\User;
use App\Models\Ustad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SantriController extends Controller
{
    private function routePrefix(): string
    {
        $seg = request()->segment(1);

        return in_array($seg, ['admin', 'ustad', 'orangtua'], true) ? $seg.'.' : 'admin.';
    }

    public function index(Request $request)
    {
        $query = Santri::with(['ustad', 'orangTua'])->latest();

        // Ustad hanya melihat santri bimbingannya sendiri
        if (auth()->check() && auth()->user()->role === 'ustad') {
            $ustadAktif = Ustad::where('user_id', auth()->id())->first();
            $query->where('ustad_id', $ustadAktif?->id ?? -1);
        }

        // Orang tua hanya melihat anaknya sendiri
        if (auth()->check() && auth()->user()->role === 'orang_tua') {
            $ortuAktif = OrangTua::where('user_id', auth()->id())->first();
            $query->where('orang_tua_id', $ortuAktif?->id ?? -1);
        }

        if ($q = $request->query('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', "%{$q}%")
                    ->orWhere('nis', 'like', "%{$q}%");
            });
        }

        if ($request->filled('ustad_id')) {
            $query->where('ustad_id', $request->query('ustad_id'));
        }

        $santri = $query->paginate(10)->withQueryString();
        $ustadList = Ustad::where('status', 'aktif')->orderBy('nama')->get();

        return view('santri.index', compact('santri', 'ustadList'));
    }

    public function create()
    {
        // Ustad tidak diperbolehkan menambah santri baru (hanya admin).
        if (auth()->check() && auth()->user()->role === 'ustad') {
            abort(403, 'Ustad tidak diperbolehkan menambah santri baru.');
        }

        $ustadList = Ustad::where('status', 'aktif')->orderBy('nama')->get();
        $ustadAktif = null;
        if (auth()->check() && auth()->user()->role === 'ustad') {
            $ustadAktif = Ustad::where('user_id', auth()->id())->firstOrFail();
        }

        return view('santri.create', compact('ustadList', 'ustadAktif'));
    }

    public function store(Request $request)
    {
        // Ustad tidak diperbolehkan menambah santri baru (hanya admin).
        if (auth()->check() && auth()->user()->role === 'ustad') {
            abort(403, 'Ustad tidak diperbolehkan menambah santri baru.');
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nis' => ['required', 'string', 'max:30', 'unique:santri,nis'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tanggal_lahir' => ['nullable', 'date'],
            'alamat' => ['nullable', 'string'],
            'tanggal_masuk' => ['nullable', 'date'],
            'target_hafalan' => ['nullable', 'integer', 'min:1', 'max:30'],
            'status' => ['nullable', 'in:aktif,lulus,keluar'],
            // Wali santri diisi manual (1 santri = 1 wali baru)
            'wali_nama' => ['required', 'string', 'max:255'],
            'wali_email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'wali_nomor_hp' => ['nullable', 'string', 'max:20'],
            'wali_password' => ['required', 'string', 'min:8', 'max:255'],
            // Ustad dipilih dari data yang sudah terdaftar (tidak membuat baru)
            'ustad_id' => ['required', 'exists:ustad,id'],
        ]);

        $santri = DB::transaction(function () use ($validated, $request) {
            $userWali = new User();
            $userWali->name = $validated['wali_nama'];
            $userWali->email = $validated['wali_email'] ?? str()->slug($validated['wali_nama']).'-'.time().'@pesantren.local';
            $userWali->password = $validated['wali_password'];
            $userWali->role = 'orang_tua';
            $userWali->save();

            $orangTua = OrangTua::create([
                'user_id' => $userWali->id,
                'nama' => $validated['wali_nama'],
                'nomor_hp' => $validated['wali_nomor_hp'] ?? null,
                'alamat' => $validated['alamat'] ?? $request->input('wali_alamat'),
                'status' => 'aktif',
            ]);

            return Santri::create([
                'nama' => $validated['nama'],
                'nis' => $validated['nis'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'orang_tua_id' => $orangTua->id,
                'ustad_id' => $validated['ustad_id'],
                'tanggal_masuk' => $validated['tanggal_masuk'] ?? null,
                'target_hafalan' => $validated['target_hafalan'] ?? 30,
                'status' => $validated['status'] ?? 'aktif',
            ]);
        });

        return redirect()->route($this->routePrefix().'santri.show', $santri)->with('success', 'Data santri + wali santri berhasil ditambahkan.');
    }

    public function show(Santri $santri)
    {
        if (auth()->check() && auth()->user()->role === 'ustad') {
            $ustadAktif = Ustad::where('user_id', auth()->id())->first();
            if (! $ustadAktif || (int) $santri->ustad_id !== (int) $ustadAktif->id) {
                abort(403, 'Santri bukan bimbingan Anda.');
            }
        }

        if (auth()->check() && auth()->user()->role === 'orang_tua') {
            $ortuAktif = OrangTua::where('user_id', auth()->id())->first();
            if (! $ortuAktif || (int) $santri->orang_tua_id !== (int) $ortuAktif->id) {
                abort(403, 'Anda tidak berhak mengakses data santri ini.');
            }
        }

        $santri->load(['ustad', 'orangTua']);

        $setoranTerbaru = $santri->setoran()->with('ustad')->latest('tanggal_setoran')->latest('id')->take(10)->get();

        $juzUnik = $santri->setoran()->whereNotNull('juz_mulai')->distinct()->count('juz_mulai');
        $target = max(1, (int) $santri->target_hafalan);
        $progress = min(100, round($juzUnik / $target * 100, 1));
        $totalSetoran = $santri->setoran()->count();

        return view('santri.show', compact('santri', 'setoranTerbaru', 'juzUnik', 'progress', 'totalSetoran'));
    }

    public function edit(Santri $santri)
    {
        $ustadList = Ustad::where('status', 'aktif')->orderBy('nama')->get();
        $santri->load(['orangTua.user', 'ustad']);

        return view('santri.edit', compact('santri', 'ustadList'));
    }

    public function update(Request $request, Santri $santri)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nis' => ['required', 'string', 'max:30', 'unique:santri,nis,' . $santri->id],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tanggal_lahir' => ['nullable', 'date'],
            'alamat' => ['nullable', 'string'],
            'tanggal_masuk' => ['nullable', 'date'],
            'target_hafalan' => ['nullable', 'integer', 'min:1', 'max:30'],
            'status' => ['nullable', 'in:aktif,lulus,keluar'],
            // Wali santri diedit manual (update data orang_tua yang terrelasi)
            'wali_nama' => ['required', 'string', 'max:255'],
            'wali_email' => ['nullable', 'email', 'max:255', 'unique:users,email,'.optional(optional($santri->orangTua)->user)->id],
            'wali_nomor_hp' => ['nullable', 'string', 'max:20'],
            'wali_password' => ['nullable', 'string', 'min:8', 'max:255'],
            // Ustad dipilih dari data yang sudah terdaftar (tidak membuat baru)
            'ustad_id' => ['required', 'exists:ustad,id'],
        ]);

        DB::transaction(function () use ($validated, $santri, $request) {
            $santri->update([
                'nama' => $validated['nama'],
                'nis' => $validated['nis'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'ustad_id' => $validated['ustad_id'],
                'tanggal_masuk' => $validated['tanggal_masuk'] ?? null,
                'target_hafalan' => $validated['target_hafalan'] ?? 30,
                'status' => $validated['status'] ?? $santri->status,
            ]);

            $orangTua = $santri->orangTua()->first();

            if ($orangTua) {
                if ($orangTua->user) {
                    $orangTua->user->update(array_filter([
                        'name' => $validated['wali_nama'],
                        'email' => $validated['wali_email'] ?? $orangTua->user->email,
                        'password' => $validated['wali_password'] ?? null,
                    ]));
                }
                $orangTua->update([
                    'nama' => $validated['wali_nama'],
                    'nomor_hp' => $validated['wali_nomor_hp'] ?? null,
                    'alamat' => $validated['alamat'] ?? $request->input('wali_alamat'),
                ]);
            } else {
                $user = new User();
                $user->name = $validated['wali_nama'];
                $user->email = $validated['wali_email'] ?? str()->slug($validated['wali_nama']).'-'.time().'@pesantren.local';
                $user->password = $validated['wali_password'] ?? 'password';
                $user->role = 'orang_tua';
                $user->save();

                $baru = OrangTua::create([
                    'user_id' => $user->id,
                    'nama' => $validated['wali_nama'],
                    'nomor_hp' => $validated['wali_nomor_hp'] ?? null,
                    'alamat' => $validated['alamat'] ?? $request->input('wali_alamat'),
                    'status' => 'aktif',
                ]);
                $santri->update(['orang_tua_id' => $baru->id]);
            }
        });

        return redirect()->route($this->routePrefix().'santri.show', $santri)->with('success', 'Data santri berhasil diperbarui.');
    }

    public function destroy(Santri $santri)
    {
        $santri->delete();

        return redirect()->route($this->routePrefix().'santri.index')->with('success', 'Data santri berhasil dihapus.');
    }
}
