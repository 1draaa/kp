<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Setoran;
use App\Models\Ustad;
use Illuminate\Http\Request;

class SetoranController extends Controller
{
    private function routePrefix(): string
    {
        $seg = request()->segment(1);

        return in_array($seg, ['admin', 'ustad', 'orangtua'], true) ? $seg.'.' : 'admin.';
    }

    private function scopeByRole($query)
    {
        $user = auth()->user();

        if ($user->role === 'ustad') {
            $ustad = Ustad::where('user_id', $user->id)->first();
            $query->where('setoran.ustad_id', $ustad?->id ?? -1);
        } elseif ($user->role === 'orang_tua') {
            $anakIds = Santri::whereHas('orangTua', fn ($q) => $q->where('user_id', $user->id))->pluck('santri.id');
            $query->whereIn('setoran.santri_id', $anakIds->isEmpty() ? [-1] : $anakIds->all());
        }

        return $query;
    }

    private function santriOptions(): \Illuminate\Database\Eloquent\Collection
    {
        $user = auth()->user();

        if ($user->role === 'ustad') {
            $ustad = Ustad::where('user_id', $user->id)->first();
            return Santri::where('ustad_id', $ustad?->id)->orderBy('nama')->get();
        }

        if ($user->role === 'orang_tua') {
            return Santri::whereHas('orangTua', fn ($q) => $q->where('user_id', $user->id))->orderBy('nama')->get();
        }

        return Santri::orderBy('nama')->get();
    }

    private function authorizeSetoran(Setoran $setoran): void
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return;
        }

        if ($user->role === 'ustad') {
            $ustad = Ustad::where('user_id', $user->id)->first();
            if (! $ustad || (int) $setoran->ustad_id !== (int) $ustad->id) {
                abort(403, 'Anda tidak berhak mengakses setoran ini.');
            }
            return;
        }

        if ($user->role === 'orang_tua') {
            $allowed = Santri::where('id', $setoran->santri_id)
                ->whereHas('orangTua', fn ($q) => $q->where('user_id', $user->id))
                ->exists();
            if (! $allowed) {
                abort(403, 'Anda tidak berhak mengakses setoran ini.');
            }
            return;
        }

        abort(403);
    }

    public function index(Request $request)
    {
        $query = Setoran::with(['santri', 'ustad'])->latest('tanggal_setoran')->latest('id');
        $this->scopeByRole($query);

        if ($request->filled('jenis_setoran')) {
            $query->where('jenis_setoran', $request->query('jenis_setoran'));
        }
        if ($request->filled('santri_id')) {
            $query->where('santri_id', $request->query('santri_id'));
        }
        if ($request->filled('dari')) {
            $query->whereDate('tanggal_setoran', '>=', $request->query('dari'));
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal_setoran', '<=', $request->query('sampai'));
        }
        if ($q = $request->query('q')) {
            $query->whereHas('santri', fn ($w) => $w->where('nama', 'like', "%{$q}%"));
        }

        $setoran = $query->paginate(10)->withQueryString();
        $santriList = $this->santriOptions();

        return view('setoran.index', compact('setoran', 'santriList'));
    }

    public function create()
    {
        $santriList = $this->santriOptions();
        $ustadList = Ustad::where('status', 'aktif')->orderBy('nama')->get();

        return view('setoran.create', compact('santriList', 'ustadList'));
    }

    public function store(Request $request)
    {
        $jenis = $request->input('jenis_setoran');

        $rules = [
            'santri_id' => ['required', 'exists:santri,id'],
            'ustad_id' => ['nullable', 'exists:ustad,id'],
            'jenis_setoran' => ['required', 'in:sabaq,sabqy,qiroah,manzil'],
            'tanggal_setoran' => ['required', 'date'],
            'juz_mulai' => ['nullable', 'integer', 'min:1', 'max:30'],
            'juz_selesai' => ['nullable', 'integer', 'min:1', 'max:30'],
            'surat_mulai' => ['nullable', 'string', 'max:60'],
            'surat_selesai' => ['nullable', 'string', 'max:60'],
            'ayat_mulai' => ['nullable', 'integer', 'min:1'],
            'ayat_selesai' => ['nullable', 'integer', 'min:1'],
            'jumlah_halaman' => ['nullable', 'integer', 'min:1'],
            'kelancaran' => ['nullable', 'integer', 'min:0', 'max:100'],
            'tajwid' => ['nullable', 'integer', 'min:0', 'max:100'],
            'makhraj' => ['nullable', 'integer', 'min:0', 'max:100'],
            'jumlah_kesalahan' => ['nullable', 'integer', 'min:0'],
            'nilai' => ['nullable', 'integer', 'min:0', 'max:100'],
            'status' => ['nullable', 'string', 'max:20'],
            'catatan' => ['nullable', 'string'],
        ];

        if (in_array($jenis, ['sabaq', 'sabqy'])) {
            $rules['surat_mulai'] = ['required', 'string', 'max:60'];
            $rules['ayat_mulai'] = ['required', 'integer', 'min:1'];
            $rules['ayat_selesai'] = ['required', 'integer', 'min:1'];
            $rules['juz_mulai'] = ['required', 'integer', 'min:1', 'max:30'];
        } elseif ($jenis === 'qiroah') {
            $rules['jumlah_kesalahan'] = ['required', 'integer', 'min:0'];
        } elseif ($jenis === 'manzil') {
            $rules['juz_mulai'] = ['required', 'integer', 'min:1', 'max:30'];
            $rules['juz_selesai'] = ['required', 'integer', 'min:1', 'max:30'];
        }

        $validated = $request->validate($rules);

        $user = auth()->user();
        if ($user->role === 'ustad') {
            $ustad = Ustad::where('user_id', $user->id)->firstOrFail();
            // Pastikan santri yang disetor adalah santri bimbingannya
            $milik = Santri::where('id', $validated['santri_id'])->where('ustad_id', $ustad->id)->exists();
            abort_unless($milik, 403, 'Santri bukan bimbingan Anda.');
            $validated['ustad_id'] = $ustad->id;
        } else {
            $request->validate(['ustad_id' => ['required', 'exists:ustad,id']]);
            $validated['ustad_id'] = $request->input('ustad_id');
        }

        $setoran = Setoran::create($validated);

        return redirect()->route($this->routePrefix().'setoran.show', $setoran)->with('success', 'Setoran berhasil dicatat.');
    }

    public function show(Setoran $setoran)
    {
        $this->authorizeSetoran($setoran);
        $setoran->load(['santri', 'ustad']);

        return view('setoran.show', compact('setoran'));
    }

    public function edit(Setoran $setoran)
    {
        $this->authorizeSetoran($setoran);
        $santriList = $this->santriOptions();
        $ustadList = Ustad::where('status', 'aktif')->orderBy('nama')->get();

        return view('setoran.edit', compact('setoran', 'santriList', 'ustadList'));
    }

    public function update(Request $request, Setoran $setoran)
    {
        $this->authorizeSetoran($setoran);

        $jenis = $request->input('jenis_setoran', $setoran->jenis_setoran);

        $rules = [
            'santri_id' => ['required', 'exists:santri,id'],
            'ustad_id' => ['nullable', 'exists:ustad,id'],
            'jenis_setoran' => ['required', 'in:sabaq,sabqy,qiroah,manzil'],
            'tanggal_setoran' => ['required', 'date'],
            'juz_mulai' => ['nullable', 'integer', 'min:1', 'max:30'],
            'juz_selesai' => ['nullable', 'integer', 'min:1', 'max:30'],
            'surat_mulai' => ['nullable', 'string', 'max:60'],
            'surat_selesai' => ['nullable', 'string', 'max:60'],
            'ayat_mulai' => ['nullable', 'integer', 'min:1'],
            'ayat_selesai' => ['nullable', 'integer', 'min:1'],
            'jumlah_halaman' => ['nullable', 'integer', 'min:1'],
            'kelancaran' => ['nullable', 'integer', 'min:0', 'max:100'],
            'tajwid' => ['nullable', 'integer', 'min:0', 'max:100'],
            'makhraj' => ['nullable', 'integer', 'min:0', 'max:100'],
            'jumlah_kesalahan' => ['nullable', 'integer', 'min:0'],
            'nilai' => ['nullable', 'integer', 'min:0', 'max:100'],
            'status' => ['nullable', 'string', 'max:20'],
            'catatan' => ['nullable', 'string'],
        ];

        if (in_array($jenis, ['sabaq', 'sabqy'])) {
            $rules['surat_mulai'] = ['required', 'string', 'max:60'];
            $rules['ayat_mulai'] = ['required', 'integer', 'min:1'];
            $rules['ayat_selesai'] = ['required', 'integer', 'min:1'];
            $rules['juz_mulai'] = ['required', 'integer', 'min:1', 'max:30'];
        } elseif ($jenis === 'qiroah') {
            $rules['jumlah_kesalahan'] = ['required', 'integer', 'min:0'];
        } elseif ($jenis === 'manzil') {
            $rules['juz_mulai'] = ['required', 'integer', 'min:1', 'max:30'];
            $rules['juz_selesai'] = ['required', 'integer', 'min:1', 'max:30'];
        }

        $validated = $request->validate($rules);

        if (auth()->user()->role === 'ustad') {
            $ustad = Ustad::where('user_id', auth()->id())->firstOrFail();
            $validated['ustad_id'] = $ustad->id;
        }

        $setoran->update($validated);

        return redirect()->route($this->routePrefix().'setoran.show', $setoran)->with('success', 'Setoran berhasil diperbarui.');
    }

    public function destroy(Setoran $setoran)
    {
        $this->authorizeSetoran($setoran);
        $setoran->delete();

        return redirect()->route($this->routePrefix().'setoran.index')->with('success', 'Setoran berhasil dihapus.');
    }
}
