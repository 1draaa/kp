@php
    $seg = request()->segment(1);
    $prefix = $seg === 'ustad' ? 'ustad.' : ($seg === 'orangtua' ? 'orangtua.' : 'admin.');
    $role = $seg === 'ustad' ? 'ustad' : ($seg === 'orangtua' ? 'orang_tua' : 'admin');
    $canManage = $prefix === 'ustad.';
@endphp
<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Setoran</h2></x-slot>
    <div class="flex">
        @include('layouts.sidebar', ['role' => $role])
        <div class="flex-1 py-6 px-4 sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>@endif
            @if (session('error'))<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{{ session('error') }}</div>@endif
            <form method="GET" class="bg-white p-4 rounded-lg shadow grid sm:grid-cols-5 gap-2 text-sm">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari santri..." class="border-gray-300 rounded-md shadow-sm" />
                <select name="jenis_setoran" class="border-gray-300 rounded-md shadow-sm"><option value="">Semua Jenis</option>@foreach (['sabaq','sabqy','qiroah','manzil'] as $j)<option value="{{ $j }}" @selected(request('jenis_setoran', request('jenis'))==$j)>{{ strtoupper($j) }}</option>@endforeach</select>
                <select name="santri_id" class="border-gray-300 rounded-md shadow-sm"><option value="">Semua Santri</option>@foreach ($santriList ?? [] as $st)<option value="{{ $st->id }}" @selected(request('santri_id')==$st->id)>{{ $st->nama }}</option>@endforeach</select>
                <input type="date" name="dari" value="{{ request('dari') }}" class="border-gray-300 rounded-md shadow-sm" />
                <input type="date" name="sampai" value="{{ request('sampai') }}" class="border-gray-300 rounded-md shadow-sm" />
                <div class="sm:col-span-5 flex gap-2">
                    <button class="bg-gray-800 text-white px-3 py-1.5 rounded text-sm">Filter</button>
                    <a href="{{ route($prefix.'setoran.index') }}" class="px-3 py-1.5 border rounded text-sm">Reset</a>
                    @if ($canManage)<a href="{{ route($prefix.'setoran.create') }}" class="ml-auto bg-indigo-600 text-white px-3 py-1.5 rounded text-sm">+ Input Setoran</a>@endif
                </div>
            </form>
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b text-left text-gray-500">
                        <th class="py-2 px-3">Tanggal</th><th class="py-2 px-3">Santri</th><th class="py-2 px-3">Jenis</th><th class="py-2 px-3">Materi</th><th class="py-2 px-3">Status</th><th class="py-2 px-3">Aksi</th>
                    </tr></thead>
                    <tbody>
                        @forelse ($setoran ?? [] as $s)
                            <tr class="border-b">
                                <td class="py-2 px-3">{{ optional($s->tanggal_setoran)->format('Y-m-d') }}</td>
                                <td class="py-2 px-3">{{ $s->santri->nama ?? '-' }}</td>
                                <td class="py-2 px-3">{{ strtoupper($s->jenis_setoran) }}</td>
                                <td class="py-2 px-3">{{ $s->ringkasanMateri() }}</td>
                                <td class="py-2 px-3">{{ $s->status ? ucfirst($s->status) : '-' }}</td>
                                <td class="py-2 px-3 space-x-2 whitespace-nowrap">
                                    <a href="{{ route($prefix.'setoran.show', $s) }}" class="text-indigo-600 hover:underline">Detail</a>
                                    @if ($canManage)
                                        <a href="{{ route($prefix.'setoran.edit', $s) }}" class="text-yellow-600 hover:underline">Edit</a>
                                        <form action="{{ route($prefix.'setoran.destroy', $s) }}" method="POST" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-600 hover:underline">Hapus</button></form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-3 px-3 text-gray-500">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ ($setoran ?? null)?->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>
