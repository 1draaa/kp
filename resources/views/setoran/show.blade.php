@php
    $seg = request()->segment(1);
    $prefix = $seg === 'ustad' ? 'ustad.' : ($seg === 'orangtua' ? 'orangtua.' : 'admin.');
    $role = $seg === 'ustad' ? 'ustad' : ($seg === 'orangtua' ? 'orang_tua' : 'admin');
@endphp
<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Setoran</h2></x-slot>
    <div class="flex">
        @include('layouts.sidebar', ['role' => $role])
        <div class="flex-1 py-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>@endif
            <div class="bg-white p-6 rounded-lg shadow grid sm:grid-cols-2 gap-2 text-sm">
                <p><span class="text-gray-500">Santri:</span> {{ $setoran->santri->nama ?? '-' }}</p>
                <p><span class="text-gray-500">Ustad:</span> {{ $setoran->ustad->nama ?? '-' }}</p>
                <p><span class="text-gray-500">Jenis:</span> {{ strtoupper($setoran->jenis_setoran) }}</p>
                <p><span class="text-gray-500">Tanggal:</span> {{ optional($setoran->tanggal_setoran)->format('Y-m-d') }}</p>
                <p><span class="text-gray-500">Surat:</span> {{ $setoran->surat_mulai ?? '-' }} - {{ $setoran->surat_selesai ?? '-' }}</p>
                <p><span class="text-gray-500">Ayat:</span> {{ $setoran->ayat_mulai ?? '-' }} - {{ $setoran->ayat_selesai ?? '-' }}</p>
                <p><span class="text-gray-500">Juz:</span> {{ $setoran->juz_mulai ?? '-' }} - {{ $setoran->juz_selesai ?? '-' }}</p>
                <p><span class="text-gray-500">Jumlah Halaman:</span> {{ $setoran->jumlah_halaman ?? '-' }}</p>
                <p><span class="text-gray-500">Jumlah Kesalahan:</span> {{ $setoran->jumlah_kesalahan ?? '-' }}</p>
                <p><span class="text-gray-500">Status:</span> {{ $setoran->status ? ucfirst($setoran->status) : '-' }}</p>
                <p class="sm:col-span-2"><span class="text-gray-500">Catatan:</span> {{ $setoran->catatan ?? '-' }}</p>
            </div>
            <a href="{{ route($prefix.'setoran.index') }}" class="inline-block mt-4 text-sm text-gray-600 hover:underline">&larr; Kembali</a>
        </div>
    </div>
</x-app-layout>
