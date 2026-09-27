@php $prefix = request()->segment(1) === 'ustad' ? 'ustad.' : 'admin.'; @endphp
<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Ustad</h2></x-slot>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto">
        @if (session('error'))<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>@endif
        <form action="{{ route($prefix.'ustad.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow space-y-4">
            @csrf
            <div><label class="block text-sm font-medium">Nama</label><input name="nama" value="{{ old('nama') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />@error('nama')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div><label class="block text-sm font-medium">Email</label><input type="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />@error('email')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div><label class="block text-sm font-medium">Nomor HP</label><input name="nomor_hp" value="{{ old('nomor_hp') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />@error('nomor_hp')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div><label class="block text-sm font-medium">Alamat</label><textarea name="alamat" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('alamat') }}</textarea>@error('alamat')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div><label class="block text-sm font-medium">Status</label><select name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"><option value="aktif" @selected(old('status')==='aktif')>Aktif</option><option value="nonaktif" @selected(old('status')==='nonaktif')>Nonaktif</option></select>@error('status')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
            <div class="flex gap-2"><button class="bg-indigo-600 text-white px-4 py-2 rounded">Simpan</button><a href="{{ route($prefix.'ustad.index') }}" class="px-4 py-2 border rounded">Batal</a></div>
        </form>
    </div>
</x-app-layout>
