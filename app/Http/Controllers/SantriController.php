<?php

namespace App\Http\Controllers;

use App\Models\OrangTua;
use App\Models\Santri;
use App\Models\Ustad;
use Illuminate\Http\Request;

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
        $ustadList = Ustad::where('status', 'aktif')->orderBy('nama')->get();
        $orangTuaList = OrangTua::orderBy('nama')->get();
        $ustadAktif = null;
        if (auth()->check() && auth()->user()->role === 'ustad') {
            $ustadAktif = Ustad::where('user_id', auth()->id())->firstOrFail();
        }

        return view('santri.create', compact('ustadList', 'orangTuaList', 'ustadAktif'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nis' => ['required', 'string', 'max:30', 'unique:santri,nis'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tanggal_lahir' => ['nullable', 'date'],
            'alamat' => ['nullable', 'string'],
            'orang_tua_id' => ['nullable', 'exists:orang_tua,id'],
            'ustad_id' => ['nullable', 'exists:ustad,id'],
            'tanggal_masuk' => ['nullable', 'date'],
            'target_hafalan' => ['nullable', 'integer', 'min:1', 'max:30'],
            'status' => ['nullable', 'in:aktif,lulus,keluar'],
        ]);

        // Jika yang input ustad, santri otomatis masuk bimbingannya
        if (auth()->check() && auth()->user()->role === 'ustad') {
            $ustadAktif = Ustad::where('user_id', auth()->id())->firstOrFail();
            $validated['ustad_id'] = $ustadAktif->id;
        }

        $santri = Santri::create($validated);

        return redirect()->route($this->routePrefix().'santri.show', $santri)->with('success', 'Data santri berhasil ditambahkan.');
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
        $nilaiRata2 = round((float) $santri->setoran()->avg('nilai'), 1);
        $totalSetoran = $santri->setoran()->count();

        return view('santri.show', compact('santri', 'setoranTerbaru', 'juzUnik', 'progress', 'nilaiRata2', 'totalSetoran'));
    }

    public function edit(Santri $santri)
    {
        $ustadList = Ustad::where('status', 'aktif')->orderBy('nama')->get();
        $orangTuaList = OrangTua::orderBy('nama')->get();

        return view('santri.edit', compact('santri', 'ustadList', 'orangTuaList'));
    }

    public function update(Request $request, Santri $santri)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nis' => ['required', 'string', 'max:30', 'unique:santri,nis,' . $santri->id],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tanggal_lahir' => ['nullable', 'date'],
            'alamat' => ['nullable', 'string'],
            'orang_tua_id' => ['nullable', 'exists:orang_tua,id'],
            'ustad_id' => ['nullable', 'exists:ustad,id'],
            'tanggal_masuk' => ['nullable', 'date'],
            'target_hafalan' => ['nullable', 'integer', 'min:1', 'max:30'],
            'status' => ['nullable', 'in:aktif,lulus,keluar'],
        ]);

        $santri->update($validated);

        return redirect()->route($this->routePrefix().'santri.show', $santri)->with('success', 'Data santri berhasil diperbarui.');
    }

    public function destroy(Santri $santri)
    {
        $santri->delete();

        return redirect()->route($this->routePrefix().'santri.index')->with('success', 'Data santri berhasil dihapus.');
    }
}
