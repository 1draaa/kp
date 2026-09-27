<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard Admin</h2>
    </x-slot>

    <div class="flex">
        @include('layouts.sidebar', ['role' => 'admin'])
        <div class="flex-1 py-6 px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{{ session('error') }}</div>
            @endif

            @php
                $cards = [
                    ['Total Santri', $totalSantri ?? 0],
                    ['Total Ustad', $totalUstad ?? 0],
                    ['Total Orang Tua', $totalOrangTua ?? 0],
                    ['Total Setoran', $totalSetoran ?? 0],
                    ['Setoran Hari Ini', $setoranHariIni ?? 0],
                    ['Sabaq', ($countPerJenis['sabaq'] ?? 0)],
                    ['Sabqy', ($countPerJenis['sabqy'] ?? 0)],
                    ['Qiroah', ($countPerJenis['qiroah'] ?? 0)],
                    ['Manzil', ($countPerJenis['manzil'] ?? 0)],
                ];
            @endphp
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4">
                @foreach ($cards as [$label, $val])
                    <div class="bg-white p-4 rounded-lg shadow">
                        <p class="text-sm text-gray-500">{{ $label }}</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $val }}</p>
                    </div>
                @endforeach
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="font-semibold mb-2">Setoran per Bulan</h3>
                    <canvas id="chartBulanan"></canvas>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="font-semibold mb-2">Setoran per Jenis</h3>
                    <canvas id="chartJenis"></canvas>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div class="bg-white p-4 rounded-lg shadow overflow-x-auto">
                    <h3 class="font-semibold mb-2">Setoran Terbaru</h3>
                    <table class="min-w-full text-sm">
                        <thead><tr class="border-b text-left text-gray-500">
                            <th class="py-2 pr-2">Tanggal</th><th class="py-2 pr-2">Santri</th><th class="py-2 pr-2">Jenis</th><th class="py-2">Nilai</th>
                        </tr></thead>
                        <tbody>
                            @forelse ($setoranTerbaru ?? [] as $s)
                                <tr class="border-b">
                                    <td class="py-2 pr-2">{{ optional($s->tanggal_setoran)->format('Y-m-d') }}</td>
                                    <td class="py-2 pr-2">{{ $s->santri->nama ?? '-' }}</td>
                                    <td class="py-2 pr-2">{{ strtoupper($s->jenis_setoran) }}</td>
                                    <td class="py-2">{{ $s->nilai ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="py-2 text-gray-500">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="bg-white p-4 rounded-lg shadow overflow-x-auto">
                    <h3 class="font-semibold mb-2">Santri Terbaru</h3>
                    <table class="min-w-full text-sm">
                        <thead><tr class="border-b text-left text-gray-500">
                            <th class="py-2 pr-2">Nama</th><th class="py-2 pr-2">NIS</th><th class="py-2">Ustad</th>
                        </tr></thead>
                        <tbody>
                            @forelse ($santriTerbaru ?? [] as $st)
                                <tr class="border-b">
                                    <td class="py-2 pr-2">{{ $st->nama }}</td>
                                    <td class="py-2 pr-2">{{ $st->nis }}</td>
                                    <td class="py-2">{{ $st->ustad->nama ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="py-2 text-gray-500">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        const labels = {!! json_encode($labels ?? []) !!};
        const dataBulanan = {!! json_encode($dataBulanan ?? []) !!};
        const byJenis = {!! json_encode($setoranByJenis ?? $countPerJenis ?? []) !!};
        if (document.getElementById('chartBulanan')) {
            new Chart(document.getElementById('chartBulanan'), {
                type: 'bar',
                data: { labels: labels, datasets: [{ label: 'Setoran', data: dataBulanan }] },
                options: { responsive: true }
            });
        }
        if (document.getElementById('chartJenis')) {
            new Chart(document.getElementById('chartJenis'), {
                type: 'doughnut',
                data: { labels: Object.keys(byJenis), datasets: [{ data: Object.values(byJenis) }] },
                options: { responsive: true }
            });
        }
    </script>
</x-app-layout>
