@php
    $prefix = request()->segment(1) === 'ustad' ? 'ustad.' : 'admin.';
    $isUstad = $prefix === 'ustad.';
@endphp
<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Santri</h2></x-slot>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto">
        <form action="{{ route($prefix.'santri.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow space-y-4">
            @csrf
            @if ($isUstad)
                <div class="bg-indigo-50 border border-indigo-200 text-indigo-800 text-sm px-4 py-3 rounded">
                    Santri baru ini akan otomatis masuk bimbingan Anda{{ isset($ustadAktif) ? ': <span class="font-semibold">'.$ustadAktif->nama.'</span>' : '' }}.
                </div>
            @endif
            <div><label class="block text-sm font-medium">Nama</label><input name="nama" value="{{ old('nama') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />@error('nama')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div><label class="block text-sm font-medium">NIS</label><input name="nis" value="{{ old('nis') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />@error('nis')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div><label class="block text-sm font-medium">Jenis Kelamin</label><select name="jenis_kelamin" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"><option value="L" @selected(old('jenis_kelamin')==='L')>Laki-laki</option><option value="P" @selected(old('jenis_kelamin')==='P')>Perempuan</option></select>@error('jenis_kelamin')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div><label class="block text-sm font-medium">Tanggal Lahir</label><input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />@error('tanggal_lahir')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div><label class="block text-sm font-medium">Nama Wali Santri *</label><input name="wali_nama" value="{{ old('wali_nama') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />@error('wali_nama')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div><label class="block text-sm font-medium">Email Wali (untuk login, opsional)</label><input type="email" name="wali_email" value="{{ old('wali_email') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />@error('wali_email')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div><label class="block text-sm font-medium">Password Wali (untuk login) *</label><input type="password" name="wali_password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />@error('wali_password')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div><label class="block text-sm font-medium">No. HP Wali</label><input name="wali_nomor_hp" value="{{ old('wali_nomor_hp') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />@error('wali_nomor_hp')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div><label class="block text-sm font-medium">Alamat</label><textarea name="alamat" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('alamat') }}</textarea>@error('alamat')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
            @if ($isUstad)
                @if (isset($ustadAktif))<input type="hidden" name="ustad_id" value="{{ $ustadAktif->id }}" />@endif
            @else
                <div><label class="block text-sm font-medium">Ustad (pilih dari data terdaftar)</label><select name="ustad_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"><option value="">-- Pilih Ustad --</option>@foreach ($ustadList ?? [] as $u)<option value="{{ $u->id }}" @selected(old('ustad_id')==$u->id)>{{ $u->nama }}</option>@endforeach</select>@error('ustad_id')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
            @endif
            <div><label class="block text-sm font-medium">Tanggal Masuk</label><input type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />@error('tanggal_masuk')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div><label class="block text-sm font-medium">Status</label><select name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"><option value="aktif" @selected(old('status')==='aktif')>Aktif</option><option value="lulus" @selected(old('status')==='lulus')>Lulus</option><option value="keluar" @selected(old('status')==='keluar')>Keluar</option></select>@error('status')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div class="flex gap-2"><button class="bg-indigo-600 text-white px-4 py-2 rounded">Simpan</button><a href="{{ route($prefix.'santri.index') }}" class="px-4 py-2 border rounded">Batal</a></div>
        </form>
    </div>
</x-app-layout>
