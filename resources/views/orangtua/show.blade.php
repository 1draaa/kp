@php $prefix = request()->segment(1) === 'ustad' ? 'ustad.' : 'admin.'; @endphp
<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Wali Santri</h2></x-slot>
    <div class="flex">
        @include('layouts.sidebar', ['role' => request()->segment(1) === 'ustad' ? 'ustad' : 'admin'])
        <div class="flex-1 py-6 px-4 sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>@endif
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="font-semibold text-lg">{{ $orangtua->nama }}</h3>
                <div class="grid sm:grid-cols-2 gap-2 mt-3 text-sm">
                    <p><span class="text-gray-500">Email:</span> {{ $orangtua->user->email ?? '-' }}</p>
                    <p><span class="text-gray-500">No. HP:</span> {{ $orangtua->nomor_hp ?? '-' }}</p>
                    <p><span class="text-gray-500">Alamat:</span> {{ $orangtua->alamat ?? '-' }}</p>
                    <p><span class="text-gray-500">Status:</span> {{ $orangtua->status ?? '-' }}</p>
                    <p><span class="text-gray-500">Jumlah Anak:</span> {{ $orangtua->santri->count() }}</p>
                </div>
                <div class="mt-4 flex gap-2">
                    <a href="{{ route($prefix.'orangtua.edit', $orangtua) }}" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm">Edit</a>
                    <a href="{{ route($prefix.'orangtua.index') }}" class="px-4 py-2 border rounded text-sm">Kembali</a>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow overflow-x-auto">
                <h3 class="font-semibold mb-2">Data Anak</h3>
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b text-left text-gray-500">
                        <th class="py-2 pr-2">Nama</th><th class="py-2 pr-2">NIS</th><th class="py-2 pr-2">Ustad</th><th class="py-2">Status</th>
                    </tr></thead>
                    <tbody>
                        @forelse ($orangtua->santri as $s)
                            <tr class="border-b">
                                <td class="py-2 pr-2">{{ $s->nama }}</td>
                                <td class="py-2 pr-2">{{ $s->nis }}</td>
                                <td class="py-2 pr-2">{{ $s->ustad->nama ?? '-' }}</td>
                                <td class="py-2">{{ $s->status ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-2 text-gray-500">Belum ada data anak.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
