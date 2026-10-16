@php $prefix = request()->segment(1) === 'ustad' ? 'ustad.' : 'admin.'; @endphp
<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Setoran</h2></x-slot>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto">
        <form action="{{ route($prefix.'setoran.update', $setoran) }}" method="POST" class="bg-white p-6 rounded-lg shadow space-y-4">
            @csrf @method('PUT')
            <div class="flex flex-wrap gap-2" id="jenisBtns">
                @foreach (['sabaq'=>'SABAQ','sabqy'=>'SABQY','qiroah'=>'QIROAH','manzil'=>'MANZIL'] as $v=>$l)
                    <button type="button" data-jenis="{{ $v }}" class="jenis-btn px-4 py-2 border rounded text-sm font-semibold">{{ $l }}</button>
                @endforeach
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium">Santri</label><select name="santri_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"><option value="">-- Pilih --</option>@foreach ($santriList ?? [] as $st)<option value="{{ $st->id }}" @selected(old('santri_id', $setoran->santri_id)==$st->id)>{{ $st->nama }} ({{ $st->nis }})</option>@endforeach</select>@error('santri_id')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label class="block text-sm font-medium">Jenis Setoran</label><select name="jenis_setoran" id="jenis_setoran" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">@foreach (['sabaq','sabqy','qiroah','manzil'] as $j)<option value="{{ $j }}" @selected(old('jenis_setoran', $setoran->jenis_setoran)==$j)>{{ strtoupper($j) }}</option>@endforeach</select>@error('jenis_setoran')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
                @if ($prefix === 'admin.')
                <div><label class="block text-sm font-medium">Ustad</label><select name="ustad_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"><option value="">-- Pilih --</option>@foreach ($ustadList ?? [] as $u)<option value="{{ $u->id }}" @selected(old('ustad_id', $setoran->ustad_id)==$u->id)>{{ $u->nama }}</option>@endforeach</select>@error('ustad_id')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
                @endif
                <div><label class="block text-sm font-medium">Tanggal Setoran</label><input type="date" name="tanggal_setoran" value="{{ old('tanggal_setoran', optional($setoran->tanggal_setoran)->format('Y-m-d')) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />@error('tanggal_setoran')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div class="f-surat"><label class="block text-sm font-medium">Surat Mulai</label><input name="surat_mulai" value="{{ old('surat_mulai', $setoran->surat_mulai) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" /></div>
                <div class="f-surat"><label class="block text-sm font-medium">Surat Selesai</label><input name="surat_selesai" value="{{ old('surat_selesai', $setoran->surat_selesai) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" /></div>
                <div class="f-ayat"><label class="block text-sm font-medium">Ayat Mulai</label><input type="number" name="ayat_mulai" value="{{ old('ayat_mulai', $setoran->ayat_mulai) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" /></div>
                <div class="f-ayat"><label class="block text-sm font-medium">Ayat Selesai</label><input type="number" name="ayat_selesai" value="{{ old('ayat_selesai', $setoran->ayat_selesai) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" /></div>
                <div class="f-juz"><label class="block text-sm font-medium">Juz Mulai</label><input type="number" min="1" max="30" name="juz_mulai" value="{{ old('juz_mulai', $setoran->juz_mulai) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" /></div>
                <div class="f-juz-end"><label class="block text-sm font-medium">Juz Selesai</label><input type="number" min="1" max="30" name="juz_selesai" value="{{ old('juz_selesai', $setoran->juz_selesai) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" /></div>
                <div class="f-halaman"><label class="block text-sm font-medium">Jumlah Halaman</label><input type="number" min="1" name="jumlah_halaman" id="jumlah_halaman" value="{{ old('jumlah_halaman', $setoran->jumlah_halaman) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" /></div>
                <div class="f-halaman"><label class="block text-sm font-medium">Jumlah Juz (otomatis isi halaman)</label><input type="number" min="1" max="30" name="jumlah_juz" id="jumlah_juz" value="{{ old('jumlah_juz') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" /><p class="text-xs text-gray-500">1 juz ≈ 20 halaman.</p></div>
                <div class="f-salah"><label class="block text-sm font-medium">Jumlah Kesalahan</label><input type="number" min="0" name="jumlah_kesalahan" value="{{ old('jumlah_kesalahan', $setoran->jumlah_kesalahan) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" /></div>
                <div class="f-status"><label class="block text-sm font-medium">Status</label><select name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required><option value="">-- Pilih --</option>@foreach (['lanjut','mengulang'] as $st)<option value="{{ $st }}" @selected(old('status', $setoran->status)==$st)>{{ ucfirst($st) }}</option>@endforeach</select>@error('status')<p class="text-sm text-red-600">{{ $message }}</p>@enderror</div>
            </div>
            <div><label class="block text-sm font-medium">Catatan</label><textarea name="catatan" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('catatan', $setoran->catatan) }}</textarea></div>
            <div class="flex gap-2"><button class="bg-indigo-600 text-white px-4 py-2 rounded">Update</button><a href="{{ route($prefix.'setoran.index') }}" class="px-4 py-2 border rounded">Batal</a></div>
        </form>
    </div>
    <script>
        (function () {
            const sel = document.getElementById('jenis_setoran');
            const btns = document.querySelectorAll('.jenis-btn');
            const juzInput = document.getElementById('jumlah_juz');
            const halInput = document.getElementById('jumlah_halaman');
            if (juzInput && halInput) { juzInput.addEventListener('input', () => { if (juzInput.value) halInput.value = parseInt(juzInput.value || 0) * 20; }); }
            function paint() { btns.forEach(b => { const on = b.dataset.jenis === sel.value; b.classList.toggle('bg-indigo-600', on); b.classList.toggle('text-white', on); }); }
            function toggle() {
                const j = sel.value;
                const show = (cls, v) => document.querySelectorAll(cls).forEach(e => e.style.display = v ? '' : 'none');
                if (j === 'sabaq') { show('.f-surat', true); show('.f-ayat', true); show('.f-juz', true); show('.f-juz-end', false); show('.f-halaman', false); show('.f-salah', false); show('.f-status', true); }
                else if (j === 'sabqy') { show('.f-surat', true); show('.f-ayat', true); show('.f-juz', true); show('.f-juz-end', false); show('.f-halaman', true); show('.f-salah', false); show('.f-status', true); }
                else if (j === 'qiroah') { show('.f-surat', true); show('.f-ayat', true); show('.f-juz', false); show('.f-juz-end', false); show('.f-halaman', false); show('.f-salah', true); show('.f-status', true); }
                else { show('.f-surat', true); show('.f-ayat', false); show('.f-juz', true); show('.f-juz-end', true); show('.f-halaman', false); show('.f-salah', false); show('.f-status', true); }
                paint();
            }
            btns.forEach(b => b.addEventListener('click', () => { sel.value = b.dataset.jenis; toggle(); }));
            sel.addEventListener('change', toggle); toggle();
        })();
    </script>
</x-app-layout>
