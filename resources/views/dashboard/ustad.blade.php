<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard Ustad</h2>
    </x-slot>
    <div class="flex">
        @include('layouts.sidebar', ['role' => 'ustad'])
        <div class="flex-1 py-6 px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{{ session('error') }}</div>
            @endif

            <p class="text-gray-600">Assalamu'alaikum, <span class="font-semibold">{{ $ustad->nama ?? auth()->user()->name }}</span></p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-4 rounded-lg shadow"><p class="text-sm text-gray-500">Santri Dibimbing</p><p class="text-2xl font-bold">{{ $santriDibimbing ?? 0 }}</p></div>
                <div class="bg-white p-4 rounded-lg shadow"><p class="text-sm text-gray-500">Setoran Hari Ini</p><p class="text-2xl font-bold">{{ $setoranHariIni ?? 0 }}</p></div>
                <div class="bg-white p-4 rounded-lg shadow"><p class="text-sm text-gray-500">Setoran Minggu Ini</p><p class="text-2xl font-bold">{{ $setoranMingguIni ?? 0 }}</p></div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow overflow-x-auto">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="font-semibold">Santri Bimbingan</h3>
                    <div class="flex gap-2">
                        <a href="{{ route('ustad.santri.create') }}" class="text-sm bg-green-600 text-white px-3 py-1.5 rounded">Input Santri Baru</a>
                        <a href="{{ route('ustad.setoran.create') }}" class="text-sm bg-indigo-600 text-white px-3 py-1.5 rounded">Input Setoran</a>
                    </div>
                </div>
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b text-left text-gray-500">
                        <th class="py-2 pr-2">Nama</th><th class="py-2 pr-2">NIS</th><th class="py-2 pr-2">Setoran Terakhir</th><th class="py-2">Aksi</th>
                    </tr></thead>
                    <tbody>
                        @forelse ($listSantri ?? [] as $s)
                            @php $last = $s->setoran->first() ?? null; @endphp
                            <tr class="border-b">
                                <td class="py-2 pr-2">{{ $s->nama }}</td>
                                <td class="py-2 pr-2">{{ $s->nis }}</td>
                                <td class="py-2 pr-2">{{ $last ? optional($last->tanggal_setoran)->format('Y-m-d') . ' / ' . strtoupper($last->jenis_setoran) : '-' }}</td>
                                <td class="py-2 space-x-2">
                                    <a href="{{ route('ustad.santri.show', $s) }}" class="text-indigo-600 hover:underline">Detail</a>
                                    <a href="{{ route('ustad.setoran.create', ['santri_id' => $s->id]) }}" class="text-green-600 hover:underline">Input</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-2 text-gray-500">Belum ada santri bimbingan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
