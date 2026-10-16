<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ustad;
use Illuminate\Http\Request;

class UstadController extends Controller
{
    private function routePrefix(): string
    {
        $seg = request()->segment(1);

        return in_array($seg, ['admin', 'ustad', 'orangtua'], true) ? $seg.'.' : 'admin.';
    }

    public function index(Request $request)
    {
        $query = Ustad::with('user')->latest();

        if ($q = $request->query('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', "%{$q}%")
                    ->orWhere('nomor_hp', 'like', "%{$q}%")
                    ->orWhere('niu', 'like', "%{$q}%");
            });
        }

        $ustad = $query->paginate(10)->withQueryString();

        return view('ustad.index', compact('ustad'));
    }

    public function create()
    {
        $users = User::where('role', 'ustad')
            ->whereNotIn('id', Ustad::pluck('user_id'))
            ->orderBy('name')->get();

        return view('ustad.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'niu' => ['nullable', 'string', 'max:30', 'unique:ustad,niu'],
            'nama' => ['required', 'string', 'max:255'],
            'nomor_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'status' => ['nullable', 'in:aktif,nonaktif'],
        ]);

        if (empty($validated['niu'])) {
            $validated['niu'] = 'NIU' . str_pad((string) ((int) (Ustad::max('id') ?? 0) + 1), 4, '0', STR_PAD_LEFT);
        }

        if (empty($validated['user_id'])) {
            $user = new User();
            $user->name = $validated['nama'];
            $user->email = $validated['email'] ?? str()->slug($validated['nama']) . '-' . time() . '@pesantren.local';
            $user->password = $validated['password'];
            $user->role = 'ustad';
            $user->save();
            $validated['user_id'] = $user->id;
        }

        unset($validated['email'], $validated['password']);

        $ustad = Ustad::create($validated);

        return redirect()->route($this->routePrefix().'ustad.show', $ustad)->with('success', 'Data ustad berhasil ditambahkan.');
    }

    public function show(Ustad $ustad)
    {
        $ustad->load(['user', 'santri']);
        $setoranTerbaru = $ustad->setoran()->with('santri')->latest('tanggal_setoran')->latest('id')->take(10)->get();

        return view('ustad.show', compact('ustad', 'setoranTerbaru'));
    }

    public function edit(Ustad $ustad)
    {
        $users = User::where('role', 'ustad')
            ->where(fn ($q) => $q->whereNotIn('id', Ustad::where('id', '!=', $ustad->id)->pluck('user_id')))
            ->orderBy('name')->get();

        return view('ustad.edit', compact('ustad', 'users'));
    }

    public function update(Request $request, Ustad $ustad)
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email,'.$ustad->user_id],
            'password' => ['nullable', 'string', 'min:8', 'max:255'],
            'niu' => ['required', 'string', 'max:30', 'unique:ustad,niu,'.$ustad->id],
            'nama' => ['required', 'string', 'max:255'],
            'nomor_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'status' => ['nullable', 'in:aktif,nonaktif'],
        ]);

        if ($ustad->user) {
            $ustad->user->update(array_filter([
                'name' => $validated['nama'],
                'email' => $validated['email'] ?? $ustad->user->email,
                'password' => $validated['password'] ?? null,
            ]));
        }
        unset($validated['email'], $validated['password']);

        $ustad->update($validated);

        return redirect()->route($this->routePrefix().'ustad.show', $ustad)->with('success', 'Data ustad berhasil diperbarui.');
    }

    public function destroy(Ustad $ustad)
    {
        $user = $ustad->user;
        $ustad->delete();

        if ($user) {
            $user->delete();
        }

        return redirect()->route($this->routePrefix().'ustad.index')->with('success', 'Data ustad berhasil dihapus.');
    }
}
