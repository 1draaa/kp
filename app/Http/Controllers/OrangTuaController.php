<?php

namespace App\Http\Controllers;

use App\Models\OrangTua;
use App\Models\User;
use Illuminate\Http\Request;

class OrangTuaController extends Controller
{
    private function routePrefix(): string
    {
        $seg = request()->segment(1);

        return in_array($seg, ['admin', 'ustad', 'orangtua'], true) ? $seg.'.' : 'admin.';
    }

    public function index(Request $request)
    {
        $query = OrangTua::with(['user', 'santri'])->latest();

        if ($q = $request->query('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', "%{$q}%")
                    ->orWhere('nomor_hp', 'like', "%{$q}%");
            });
        }

        $orangTua = $query->paginate(10)->withQueryString();

        return view('orangtua.index', compact('orangTua'));
    }

    public function create()
    {
        $users = User::where('role', 'orang_tua')
            ->whereNotIn('id', OrangTua::pluck('user_id'))
            ->orderBy('name')->get();

        return view('orangtua.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'nama' => ['required', 'string', 'max:255'],
            'nomor_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'status' => ['nullable', 'in:aktif,nonaktif'],
        ]);

        if (empty($validated['user_id'])) {
            $user = new User();
            $user->name = $validated['nama'];
            $user->email = $validated['email'] ?? str()->slug($validated['nama']) . '-' . time() . '@pesantren.local';
            $user->password = 'password';
            $user->role = 'orang_tua';
            $user->save();
            $validated['user_id'] = $user->id;
        }

        unset($validated['email']);

        $orangTua = OrangTua::create($validated);

        return redirect()->route($this->routePrefix().'orangtua.show', $orangTua)->with('success', 'Data wali santri berhasil ditambahkan.');
    }

    public function show(OrangTua $orangtua)
    {
        $orangtua->load(['user', 'santri.ustad']);

        return view('orangtua.show', compact('orangtua'));
    }

    public function edit(OrangTua $orangtua)
    {
        $users = User::where('role', 'orang_tua')
            ->where(fn ($q) => $q->whereNotIn('id', OrangTua::where('id', '!=', $orangtua->id)->pluck('user_id')))
            ->orderBy('name')->get();

        return view('orangtua.edit', compact('orangtua', 'users'));
    }

    public function update(Request $request, OrangTua $orangtua)
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'exists:users,id'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email,'.$orangtua->user_id],
            'nama' => ['required', 'string', 'max:255'],
            'nomor_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'status' => ['nullable', 'in:aktif,nonaktif'],
        ]);

        if ($orangtua->user) {
            $orangtua->user->update([
                'name' => $validated['nama'],
                'email' => $validated['email'] ?? $orangtua->user->email,
            ]);
        }
        unset($validated['email']);

        $orangtua->update($validated);

        return redirect()->route($this->routePrefix().'orangtua.show', $orangtua)->with('success', 'Data wali santri berhasil diperbarui.');
    }

    public function destroy(OrangTua $orangtua)
    {
        $user = $orangtua->user;
        $orangtua->delete();

        if ($user) {
            $user->delete();
        }

        return redirect()->route($this->routePrefix().'orangtua.index')->with('success', 'Data wali santri berhasil dihapus.');
    }
}
