<x-guest-layout>
    <div class="text-center mb-6">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-600 text-3xl font-bold text-white">TPQ</div>
        <h1 class="text-2xl font-bold text-gray-900">Monitoring Hafalan Al-Qur'an Santri</h1>
        <p class="mt-2 text-sm text-gray-600">
            Membantu ustad mencatat setoran <span class="font-semibold">Sabaq, Sabqy, Qiroah, Manzil</span>
            dan memudahkan wali santri memantau hafalan anak secara online.
        </p>
    </div>

    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
        <div class="rounded-xl border border-gray-200 p-4">
            <p class="font-semibold text-gray-800">Ustad</p>
            <p class="text-sm text-gray-600">Input setoran & catatan. Pantau santri bimbingan.</p>
        </div>
        <div class="rounded-xl border border-gray-200 p-4">
            <p class="font-semibold text-gray-800">Wali Santri</p>
            <p class="text-sm text-gray-600">Lihat perkembangan, riwayat & rekap hafalan anak.</p>
        </div>
        <div class="rounded-xl border border-gray-200 p-4">
            <p class="font-semibold text-gray-800">Admin</p>
            <p class="text-sm text-gray-600">Kelola ustad, santri, wali santri & seluruh data setoran.</p>
        </div>
        <div class="rounded-xl border border-gray-200 p-4">
            <p class="font-semibold text-gray-800">Rekap & Laporan</p>
            <p class="text-sm text-gray-600">Grafik perkembangan, export PDF & Excel.</p>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('login') }}"
           style="display:block;border-radius:0.5rem;background-color:#059669;padding:0.5rem 1rem;text-align:center;font-weight:600;color:#ffffff;text-decoration:none;"
           class="block rounded-lg bg-emerald-600 px-4 py-2 text-center font-semibold text-white hover:bg-emerald-700">
            Masuk
        </a>
    </div>

</x-guest-layout>
