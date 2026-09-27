@props(['role' => 'admin'])

@php
    $menus = [
        'admin' => [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Data Ustad', 'route' => 'admin.ustad.*', 'url' => route('admin.ustad.index')],
            ['label' => 'Data Santri', 'route' => 'admin.santri.*', 'url' => route('admin.santri.index')],
            ['label' => 'Data Orang Tua', 'route' => 'admin.orangtua.*', 'url' => route('admin.orangtua.index')],
            ['label' => 'Data Setoran', 'route' => 'admin.setoran.*', 'url' => route('admin.setoran.index')],
            ['label' => 'Rekap Hafalan', 'route' => 'admin.rekap.*', 'url' => route('admin.rekap.index')],
        ],
        'ustad' => [
            ['label' => 'Dashboard', 'route' => 'ustad.dashboard', 'url' => route('ustad.dashboard')],
            ['label' => 'Santri Saya', 'route' => 'ustad.santri.*', 'url' => route('ustad.santri.index')],
            ['label' => 'Input Santri Baru', 'route' => 'ustad.santri.create', 'url' => route('ustad.santri.create')],
            ['label' => 'Input Setoran', 'route' => 'ustad.setoran.create', 'url' => route('ustad.setoran.create')],
            ['label' => 'Riwayat Setoran', 'route' => 'ustad.setoran.*', 'url' => route('ustad.setoran.index')],
            ['label' => 'Rekap Hafalan', 'route' => 'ustad.rekap.*', 'url' => route('ustad.rekap.index')],
        ],
        'orang_tua' => [
            ['label' => 'Dashboard', 'route' => 'orangtua.dashboard', 'url' => route('orangtua.dashboard')],
            ['label' => 'Data Anak', 'route' => 'orangtua.santri.*', 'url' => route('orangtua.santri.index')],
            ['label' => 'Riwayat Hafalan', 'route' => 'orangtua.setoran.*', 'url' => route('orangtua.setoran.index')],
            ['label' => 'Rekap Hafalan', 'route' => 'orangtua.rekap.*', 'url' => route('orangtua.rekap.index')],
        ],
    ];
    $items = $menus[$role] ?? $menus['admin'];
    $roleLabel = $role === 'ustad' ? 'Ustad' : ucfirst(str_replace('_', ' ', $role));
@endphp

<aside class="w-64 shrink-0 bg-white border-r border-gray-200 min-h-screen hidden md:block">
    <div class="p-4 border-b border-gray-200">
        <p class="text-xs uppercase tracking-wide text-gray-500">Menu {{ $roleLabel }}</p>
    </div>
    <nav class="p-3 space-y-1">
        @foreach ($items as $m)
            <a href="{{ $m['url'] }}"
               class="block px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs($m['route']) ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                {{ $m['label'] }}
            </a>
        @endforeach
    </nav>
</aside>
