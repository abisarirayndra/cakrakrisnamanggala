    <table class="tabel" style="margin-top:5px">
        <tr>
            <th class="sel-head">No</th>
            <th class="sel-head">Nama</th>
            <th class="sel-head">Kelas</th>
            <th class="sel-head">TTL</th>
            <th class="sel-head">Alamat</th>
            <th class="sel-head">NIK</th>
            <th class="sel-head">NISN</th>
            <th class="sel-head">Asal Sekolah</th>
            <th class="sel-head">No. WA</th>
            <th class="sel-head">Ibu</th>
            <th class="sel-head">Wali</th>
            <th class="sel-head">No. WA Wali</th>
            <th class="sel-head">Markas</th>

        </tr>
        @php
        $no = 1;
        @endphp
        @foreach ($pelajar as $item)
        <tr>
            <td class="sel">{{ $no++ }}</td>
            <td class="sel">{{ $item->nama }}</td>
            <td class="sel">{{ $item->kelas }}</td>
            <td class="sel">{{ $item->tempat_lahir }}, {{ \Carbon\Carbon::parse($item->tanggal_lahir)->isoFormat('D MMMM Y') }}</td>
            <td class="sel">
                {{ $item->alamat }}
            <td class="sel">
                {{ $item->nik }}
            </td>
            <td class="sel">
                {{ $item->nisn }}
            </td>
            <td class="sel">{{ $item->sekolah }}</td>
            <td class="sel">{{ $item->wa }}</td>
            <td class="sel">{{ $item->ibu }}</td>
            <td class="sel">{{ $item->wali }}</td>
            <td class="sel">{{ $item->wa_wali }}</td>
            <td class="sel">{{ $item->markas }}</td>
        </tr>
        @endforeach
      </table>


