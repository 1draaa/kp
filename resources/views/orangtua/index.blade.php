@php $prefix = request()->segment(1) === 'ustad' ? 'ustad.' : 'admin.'; @endphp
<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Wali Santri</h2></x-slot>
    <div class="flex">
        @include('layouts.sidebar', ['role' => request()->segment(1) === 'ustad' ? 'ustad' : 'admin'])
        <div class="flex-1 py-6 px-4 sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>@endif
            @if (session('error'))<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{{ session('error') }}</div>@endif
            <div class="flex flex-wrap gap-2 justify-between items-center">
                <form method="GET" class="flex gap-2">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama/email..." class="border-gray-300 rounded-md shadow-sm text-sm" />
                    <button class="bg-gray-800 text-white px-3 py-1.5 rounded text-sm">Cari</button>
                </form>
                <a href="{{ route($prefix.'orangtua.create') }}" class="bg-indigo-600 text-white px-3 py-1.5 rounded text-sm">+ Tambah Wali Santri</a>
            </div>
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b text-left text-gray-500">
                        <th class="py-2 px-3">Nama</th><th class="py-2 px-3">Email</th><th class="py-2 px-3">No. HP</th><th class="py-2 px-3">Status</th><th class="py-2 px-3">Aksi</th>
                    </tr></thead>
                    <tbody>
                        @forelse ($orangtua ?? $orangTua ?? [] as $o)
                            <tr class="border-b">
                                <td class="py-2 px-3">{{ $o->nama }}</td>
                                <td class="py-2 px-3">{{ $o->email ?? $o->user->email ?? '-' }}</td>
                                <td class="py-2 px-3">{{ $o->nomor_hp ?? '-' }}</td>
                                <td class="py-2 px-3">{{ $o->status ?? '-' }}</td>
                                <td class="py-2 px-3 space-x-2 whitespace-nowrap">
                                    <a href="{{ route($prefix.'orangtua.show', $o) }}" class="text-indigo-600 hover:underline">Detail</a>
                                    <a href="{{ route($prefix.'orangtua.edit', $o) }}" class="text-yellow-600 hover:underline">Edit</a>
                                    <form action="{{ route($prefix.'orangtua.destroy', $o) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data ini?')">@csrf @method('DELETE')<button class="text-red-600 hover:underline">Hapus</button></form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-3 px-3 text-gray-500">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @isset($orangtua) {{ $orangtua->withQueryString()->links() }} @endisset
            @isset($orangTua) {{ $orangTua->withQueryString()->links() }} @endisset
        </div>
    </div>
</x-app-layout>
