<x-guest-layout>
    <div class="text-center mb-6">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-600 text-3xl font-bold text-white">Q</div>
        <h1 class="text-2xl font-bold text-gray-900">Sistem Monitoring Hafalan Al-Qur'an Santri</h1>
        <p class="mt-2 text-sm text-gray-600">
            Membantu ustad mencatat setoran <span class="font-semibold">Sabaq, Sabqy, Qiroah, Manzil</span>
            dan memudahkan orang tua memantau hafalan anak secara online.
        </p>
    </div>

    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
        <div class="rounded-xl border border-gray-200 p-4">
            <p class="font-semibold text-gray-800">Ustad</p>
            <p class="text-sm text-gray-600">Input setoran, nilai & catatan. Pantau santri bimbingan.</p>
        </div>
        <div class="rounded-xl border border-gray-200 p-4">
            <p class="font-semibold text-gray-800">Orang Tua</p>
            <p class="text-sm text-gray-600">Lihat perkembangan, riwayat & rekap hafalan anak.</p>
        </div>
        <div class="rounded-xl border border-gray-200 p-4">
            <p class="font-semibold text-gray-800">Admin</p>
            <p class="text-sm text-gray-600">Kelola ustad, santri, orang tua & seluruh data setoran.</p>
        </div>
        <div class="rounded-xl border border-gray-200 p-4">
            <p class="font-semibold text-gray-800">Rekap & Laporan</p>
            <p class="text-sm text-gray-600">Grafik perkembangan, export PDF & Excel.</p>
        </div>
    </div>

    <div class="mt-6 flex flex-col gap-3 sm:flex-row">
        <a href="{{ route('login') }}"
           class="flex-1 rounded-lg bg-emerald-600 px-4 py-2 text-center font-semibold text-white hover:bg-emerald-700">
            Masuk
        </a>
        <a href="{{ route('register') }}"
           class="flex-1 rounded-lg border border-emerald-600 px-4 py-2 text-center font-semibold text-emerald-700 hover:bg-emerald-50">
            Daftar (Orang Tua)
        </a>
    </div>

    <div class="mt-6 rounded-xl bg-gray-50 p-4 text-sm">
        <p class="mb-2 font-semibold text-gray-800">Akun demo <span class="font-normal text-gray-500">(password: <code>password</code>)</span></p>
        <ul class="space-y-1 text-gray-600">
            <li><code>admin@example.com</code> — Admin</li>
            <li><code>ustadahmad@example.com</code> — Ustad</li>
            <li><code>orangtuaarka@example.com</code> — Orang Tua</li>
        </ul>
    </div>
</x-guest-layout>
