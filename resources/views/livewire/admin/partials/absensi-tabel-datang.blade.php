@php
    $aksi = $aksi ?? true;
@endphp
<div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead>
            <tr>
                <th style="width: 3rem;">No</th>
                <th>Nama</th>
                <th>Status</th>
                <th>Datang</th>
                <th>Pulang</th>
                @if ($aksi)
                    <th class="text-end">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                @php
                    $orang = $tipe === 'pendidik' ? $row->pendidik : $row->pelajar;
                    $statusTampil = \App\Support\AbsensiStatus::tampilkan((int) $row->status, $row->datang, $slot?->mulai);
                    $warnaTampil = \App\Support\AbsensiStatus::warnaTampil((int) $row->status, $row->datang, $slot?->mulai);
                @endphp
                <tr wire:key="absensi-{{ $tipe }}-datang-{{ $row->id }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <span class="fw-semibold">{{ $orang?->nama }}</span>
                        @if ($tipe === 'pendidik' && $slot && (int) $slot->pendidik_id === (int) $row->pendidik_id)
                            <span class="ck-hint small d-block">Guru utama</span>
                        @endif
                        @if ($tipe === 'pendidik' && $row->jurnal)
                            <span class="ck-hint small d-block">Pengisi jurnal</span>
                        @endif
                    </td>
                    <td @if ($warnaTampil) style="color: {{ $warnaTampil }};" @endif>{{ $statusTampil }}</td>
                    <td>{{ $row->datang?->format('H:i') ?: '—' }}</td>
                    <td>{{ $row->pulang?->format('H:i') ?: '—' }}</td>
                    @if ($aksi)
                        <td>@include('livewire.admin.partials.absensi-aksi', ['tipe' => $tipe, 'row' => $row])</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $aksi ? 6 : 5 }}" class="ck-hint">Belum ada yang datang</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
