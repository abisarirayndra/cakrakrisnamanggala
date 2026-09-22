<div class="table-responsive">
    <table class="table align-middle mb-0">
        <thead>
            <tr>
                <th>Rank</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Status</th>
                @foreach ($jadwal->banks as $bank)
                    <th>{{ $bank->nama }}</th>
                @endforeach
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($baris as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="fw-semibold">{{ $item['nama'] }}</td>
                    <td>{{ $item['kelas'] }}</td>
                    <td>{{ $item['status'] }}</td>
                    @foreach ($item['banks'] as $bank)
                        <td>{{ $bank['skor'] ?? '-' }}</td>
                    @endforeach
                    <td class="fw-semibold">{{ $item['total'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 5 + $jadwal->banks->count() }}" class="ck-hint">Belum ada pelajar.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
