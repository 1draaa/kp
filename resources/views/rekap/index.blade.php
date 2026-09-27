@php
    $seg = request()->segment(1);
    $prefix = $seg === 'ustad' ? 'ustad.' : ($seg === 'orangtua' ? 'orangtua.' : 'admin.');
    $role = $seg === 'ustad' ? 'ustad' : ($seg === 'orangtua' ? 'orang_tua' : 'admin');
    $totalJuz = ($rekap ?? collect())->sum('juz_unik');
    $totalSetoran = ($setoran ?? collect())->total() ?? ($setoran ?? collect())->count();
    $totalHalaman = ($setoran ?? collect())->sum('jumlah_halaman');
    $rata2 = round((float) ($setoran ?? collect())->avg('nilai'), 1);
@endphp
<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Rekap Hafalan</h2></x-slot>
    <div class="flex">
        @include('layouts.sidebar', ['role' => $role])
        <div class="flex-1 py-6 px-4 sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>@endif
            <form method="GET" class="bg-white p-4 rounded-lg shadow grid sm:grid-cols-5 gap-2 text-sm">
                <select name="santri_id" class="border-gray-300 rounded-md shadow-sm"><option value="">Semua Santri</option>@foreach ($santriList ?? [] as $st)<option value="{{ $st->id }}" @selected(request('santri_id')==$st->id)>{{ $st->nama }}</option>@endforeach</select>
                <select name="jenis" class="border-gray-300 rounded-md shadow-sm"><option value="">Semua Jenis</option>@foreach (['sabaq','sabqy','qiroah','manzil'] as $j)<option value="{{ $j }}" @selected(request('jenis')==$j)>{{ strtoupper($j) }}</option>@endforeach</select>
                <input type="date" name="dari" value="{{ request('dari') }}" class="border-gray-300 rounded-md shadow-sm" />
                <input type="date" name="sampai" value="{{ request('sampai') }}" class="border-gray-300 rounded-md shadow-sm" />
                <input type="number" min="1" max="30" name="juz" value="{{ request('juz') }}" placeholder="Juz" class="border-gray-300 rounded-md shadow-sm" />
                <div class="sm:col-span-5 flex flex-wrap gap-2">
                    <button class="bg-gray-800 text-white px-3 py-1.5 rounded text-sm">Filter</button>
                    <a href="{{ route($prefix.'rekap.index') }}" class="px-3 py-1.5 border rounded text-sm">Reset</a>
                    <span class="flex-1"></span>
                    <a href="{{ route($prefix.'rekap.pdf', request()->query()) }}" class="bg-red-600 text-white px-3 py-1.5 rounded text-sm">Export PDF</a>
                    @if (Route::has($prefix.'rekap.excel'))
                    <a href="{{ route($prefix.'rekap.excel', request()->query()) }}" class="bg-green-600 text-white px-3 py-1.5 rounded text-sm">Export Excel</a>
                    @endif
                    <button type="button" onclick="window.print()" class="bg-gray-600 text-white px-3 py-1.5 rounded text-sm">Print</button>
                </div>
            </form>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-white p-4 rounded-lg shadow"><p class="text-sm text-gray-500">Total Setoran</p><p class="text-2xl font-bold">{{ $totalSetoran }}</p></div>
                <div class="bg-white p-4 rounded-lg shadow"><p class="text-sm text-gray-500">Total Halaman</p><p class="text-2xl font-bold">{{ $totalHalaman }}</p></div>
                <div class="bg-white p-4 rounded-lg shadow"><p class="text-sm text-gray-500">Total Juz (unik)</p><p class="text-2xl font-bold">{{ $totalJuz }}</p></div>
                <div class="bg-white p-4 rounded-lg shadow"><p class="text-sm text-gray-500">Rata-rata Nilai</p><p class="text-2xl font-bold">{{ $rata2 }}</p></div>
            </div>

            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b text-left text-gray-500">
                        <th class="py-2 px-3">Tanggal</th><th class="py-2 px-3">Santri</th><th class="py-2 px-3">Jenis</th><th class="py-2 px-3">Surat/Juz</th><th class="py-2 px-3">Hafalan</th><th class="py-2 px-3">Nilai</th><th class="py-2 px-3">Status</th><th class="py-2 px-3">Catatan</th>
                    </tr></thead>
                    <tbody>
                        @forelse ($setoran ?? [] as $s)
                            <tr class="border-b">
                                <td class="py-2 px-3">{{ optional($s->tanggal_setoran)->format('Y-m-d') }}</td>
                                <td class="py-2 px-3">{{ $s->santri->nama ?? '-' }}</td>
                                <td class="py-2 px-3">{{ strtoupper($s->jenis_setoran) }}</td>
                                <td class="py-2 px-3">{{ $s->ringkasanMateri() }}</td>
                                <td class="py-2 px-3">{{ $s->jumlah_halaman ?? '-' }} hlm</td>
                                <td class="py-2 px-3">{{ $s->nilai ?? '-' }}</td>
                                <td class="py-2 px-3">{{ $s->status ?? '-' }}</td>
                                <td class="py-2 px-3">{{ \Illuminate\Support\Str::limit($s->catatan ?? '-', 40) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="py-3 px-3 text-gray-500">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ ($setoran ?? null)?->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>
