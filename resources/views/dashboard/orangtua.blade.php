<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard Wali Santri</h2>
    </x-slot>
    <div class="flex">
        @include('layouts.sidebar', ['role' => 'orang_tua'])
        <div class="flex-1 py-6 px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{{ session('error') }}</div>
            @endif

            <p class="text-gray-600">Assalamu'alaikum, <span class="font-semibold">{{ $orangTua->nama ?? auth()->user()->name }}</span></p>

            @forelse ($anakList ?? [] as $i => $anak)
                <div class="bg-white p-4 rounded-lg shadow space-y-2">
                    <div class="flex flex-wrap justify-between items-center gap-2">
                        <h3 class="font-semibold text-lg">{{ $anak->nama }} <span class="text-sm text-gray-500">({{ $anak->nis }})</span></h3>
                        <a href="{{ route('orangtua.santri.show', $anak) }}" class="text-sm text-indigo-600 hover:underline">Lihat Detail</a>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm">
                        <div class="bg-gray-50 p-3 rounded"><p class="text-gray-500">Total Setoran</p><p class="font-bold">{{ $anak->total_setoran ?? 0 }}</p></div>
                        <div class="bg-gray-50 p-3 rounded"><p class="text-gray-500">Juz Unik</p><p class="font-bold">{{ $anak->juz_unik ?? 0 }}</p></div>
                        <div class="bg-gray-50 p-3 rounded"><p class="text-gray-500">Setoran Terakhir</p><p class="font-bold">{{ optional($anak->setoran_terakhir?->tanggal_setoran)->format('Y-m-d') ?? '-' }}</p></div>
                    </div>
                    @if (($anak->setoran ?? collect())->count())
                    <div>
                        <h4 class="text-sm font-semibold text-gray-600 mb-1">Riwayat Setoran Terakhir</h4>
                        <table class="min-w-full text-sm">
                            <thead><tr class="border-b text-left text-gray-500"><th class="py-1 pr-2">Tanggal</th><th class="py-1 pr-2">Jenis</th><th class="py-1">Status</th></tr></thead>
                            <tbody>
                                @foreach ($anak->setoran->take(5) as $s)
                                <tr class="border-b"><td class="py-1 pr-2">{{ optional($s->tanggal_setoran)->format('Y-m-d') }}</td><td class="py-1 pr-2">{{ strtoupper($s->jenis_setoran) }}</td><td class="py-1">{{ $s->status ? ucfirst($s->status) : '-' }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            @empty
                <div class="bg-white p-4 rounded-lg shadow text-gray-500">Belum ada data anak.</div>
            @endforelse
        </div>
    </div>

</x-app-layout>
