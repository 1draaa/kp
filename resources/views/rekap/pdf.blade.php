<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Rekap Hafalan</title>
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; }
h2 { text-align: center; margin-bottom: 4px; }
p.sub { text-align: center; margin-top: 0; color: #555; }
table { width: 100%; border-collapse: collapse; margin-top: 12px; }
th, td { border: 1px solid #333; padding: 5px 6px; text-align: left; }
th { background: #eee; }
</style>
</head>
<body>
<h2>Rekap Hafalan Santri</h2>
<p class="sub">Dicetak: {{ date('Y-m-d H:i') }}</p>
<table>
<thead><tr><th>No</th><th>Santri</th><th>Total Setoran</th><th>Rata-rata Nilai</th><th>Juz Unik</th></tr></thead>
<tbody>
@forelse ($rekap ?? [] as $i => $r)
<tr>
<td>{{ $i + 1 }}</td>
<td>{{ is_array($r) ? ($r['santri'] ?? '-') : ($r->santri->nama ?? $r->santri ?? '-') }}</td>
<td>{{ is_array($r) ? ($r['total_setoran'] ?? 0) : ($r->total_setoran ?? 0) }}</td>
<td>{{ is_array($r) ? ($r['rata_nilai'] ?? '-') : ($r->rata_nilai ?? '-') }}</td>
<td>{{ is_array($r) ? ($r['juz_unik'] ?? 0) : ($r->juz_unik ?? 0) }}</td>
</tr>
@empty
<tr><td colspan="5">Belum ada data.</td></tr>
@endforelse
</tbody>
</table>
</body>
</html>
