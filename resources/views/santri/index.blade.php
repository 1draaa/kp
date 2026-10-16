@php
    $seg = request()->segment(1);
    $prefix = $seg === 'ustad' ? 'ustad.' : ($seg === 'orangtua' ? 'orangtua.' : 'admin.');
    $role = $seg === 'ustad' ? 'ustad' : ($seg === 'orangtua' ? 'orang_tua' : 'admin');
    $canManage = $prefix === 'admin.';
    $canCreate = $prefix === 'admin.';
@endphp
<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Santri</h2></x-slot>
    <div class="flex">
        @include('layouts.sidebar', ['role' => $role])
        <div class="flex-1 py-6 px-4 sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>@endif
            @if (session('error'))<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{{ session('error') }}</div>@endif
            <div class="flex flex-wrap gap-2 justify-between items-center">
                <form method="GET" class="flex gap-2">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama/NIS..." class="border-gray-300 rounded-md shadow-sm text-sm" />
                    <button class="bg-gray-800 text-white px-3 py-1.5 rounded text-sm">Cari</button>
                </form>
                @if ($canCreate)<a href="{{ route($prefix.'santri.create') }}" class="bg-indigo-600 text-white px-3 py-1.5 rounded text-sm">{{ $prefix === 'ustad.' ? '+ Input Santri Baru' : '+ Tambah Santri' }}</a>@endif
            </div>
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b text-left text-gray-500">
                        <th class="py-2 px-3">Nama</th><th class="py-2 px-3">NIS</th><th class="py-2 px-3">Ustad</th><th class="py-2 px-3">Status</th><th class="py-2 px-3">Aksi</th>
                    </tr></thead>
                    <tbody>
                        @forelse ($santri ?? [] as $s)
                            <tr class="border-b">
                                <td class="py-2 px-3">{{ $s->nama }}</td>
                                <td class="py-2 px-3">{{ $s->nis }}</td>
                                <td class="py-2 px-3">{{ $s->ustad->nama ?? '-' }}</td>
                                <td class="py-2 px-3">{{ $s->status ?? '-' }}</td>
                                <td class="py-2 px-3 space-x-2 whitespace-nowrap">
                                    <a href="{{ route($prefix.'santri.show', $s) }}" class="text-indigo-600 hover:underline">Detail</a>
                                    @if ($canManage)
                                        <a href="{{ route($prefix.'santri.edit', $s) }}" class="text-yellow-600 hover:underline">Edit</a>
                                        <form action="{{ route($prefix.'santri.destroy', $s) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data ini?')">@csrf @method('DELETE')<button class="text-red-600 hover:underline">Hapus</button></form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-3 px-3 text-gray-500">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ ($santri ?? null)?->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>
