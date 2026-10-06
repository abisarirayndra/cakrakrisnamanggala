<div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead>
            <tr>
                <th style="width: 3rem;">No</th>
                <th>Nama</th>
                <th>Status</th>
                <th>Keterangan</th>
                <th class="text-end">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr wire:key="absensi-{{ $tipe }}-izin-{{ $row->id }}">
                    <td>{{ $loop->iteration }}</td>
                    <td class="fw-semibold">{{ ($tipe === 'pendidik' ? $row->pendidik : $row->pelajar)?->nama }}</td>
                    <td>{{ \App\Support\AbsensiStatus::tampilkan((int) $row->status) }}</td>
                    <td>{{ $row->keterangan ?: '—' }}</td>
                    <td>@include('livewire.admin.partials.absensi-aksi', ['tipe' => $tipe, 'row' => $row])</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="ck-hint">Belum ada izin, sakit, atau alpa</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
