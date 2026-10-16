@php
    $seg = request()->segment(1);
    $prefix = $seg === 'ustad' ? 'ustad.' : ($seg === 'orangtua' ? 'orangtua.' : 'admin.');
    $role = $seg === 'ustad' ? 'ustad' : ($seg === 'orangtua' ? 'orang_tua' : 'admin');
@endphp
<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Santri</h2></x-slot>
    <div class="flex">
        @include('layouts.sidebar', ['role' => $role])
        <div class="flex-1 py-6 px-4 sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>@endif
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="font-semibold text-lg">{{ $santri->nama }} <span class="text-sm text-gray-500">({{ $santri->nis }})</span></h3>
                <div class="grid sm:grid-cols-2 gap-2 mt-3 text-sm">
                    <p><span class="text-gray-500">Jenis Kelamin:</span> {{ $santri->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                    <p><span class="text-gray-500">Tanggal Lahir:</span> {{ optional($santri->tanggal_lahir)->format('Y-m-d') ?? '-' }}</p>
                    <p><span class="text-gray-500">Alamat:</span> {{ $santri->alamat ?? '-' }}</p>
                    <p><span class="text-gray-500">Wali Santri:</span> {{ $santri->orangTua->nama ?? '-' }}</p>
                    <p><span class="text-gray-500">Ustad:</span> {{ $santri->ustad->nama ?? '-' }}</p>
                    <p><span class="text-gray-500">Tanggal Masuk:</span> {{ optional($santri->tanggal_masuk)->format('Y-m-d') ?? '-' }}</p>
                    <p><span class="text-gray-500">Target Hafalan:</span> {{ $santri->target_hafalan ?? '-' }} Juz</p>
                    <p><span class="text-gray-500">Status:</span> {{ $santri->status ?? '-' }}</p>
                    <p><span class="text-gray-500">Total Setoran:</span> {{ $totalSetoran ?? 0 }}</p>
                </div>
                <div class="mt-4">
                    <div class="flex justify-between text-sm mb-1"><span>Progress Hafalan ({{ $juzUnik ?? 0 }}/{{ $santri->target_hafalan ?? 0 }} Juz)</span><span>{{ $progress ?? 0 }}%</span></div>
                    <div class="w-full bg-gray-200 rounded-full h-3"><div class="bg-indigo-600 h-3 rounded-full" style="width: {{ $progress ?? 0 }}%"></div></div>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow overflow-x-auto">
                <h3 class="font-semibold mb-2">Riwayat Setoran</h3>
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b text-left text-gray-500">
                        <th class="py-2 pr-2">Tanggal</th><th class="py-2 pr-2">Jenis</th><th class="py-2 pr-2">Materi</th><th class="py-2">Status</th>
                    </tr></thead>
                    <tbody>
                        @forelse ($setoranTerbaru ?? [] as $s)
                            <tr class="border-b">
                                <td class="py-2 pr-2">{{ optional($s->tanggal_setoran)->format('Y-m-d') }}</td>
                                <td class="py-2 pr-2">{{ strtoupper($s->jenis_setoran) }}</td>
                                <td class="py-2 pr-2">{{ $s->ringkasanMateri() }}</td>
                                <td class="py-2">{{ $s->status ? ucfirst($s->status) : '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-2 text-gray-500">Belum ada setoran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <a href="{{ route($prefix.'santri.index') }}" class="inline-block text-sm text-gray-600 hover:underline">&larr; Kembali</a>
        </div>
    </div>
</x-app-layout>
