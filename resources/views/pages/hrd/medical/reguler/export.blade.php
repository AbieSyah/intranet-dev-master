<table>
    <thead>
    <tr>
        <th align="center">NO</th>
        <th align="center">NAMA</th>
        <th align="center">LOKASI MCU</th>
        <th align="center">BAGIAN</th>
        <th align="center">TAHUN MCU</th>
        <th align="center">FITNES STATUS</th>
        <th align="center">REMARK</th>
    </tr>
    </thead>
    <tbody>
    @foreach($query as $data)
        <tr>
            <td rowspan="3" align="center">{{$loop->iteration}}</td>
            <td rowspan="3" align="center">{{$data['nama']}}</td>
            <td rowspan="3" align="center">{{$data['lokasi']}}</td>
            <td rowspan="3" align="center">{{$data['bagian']}}</td>
            @foreach($data['tahun'] as $key_tahun => $val_tahun)
                @if($key_tahun == 1)
                    <td align="center">{{$val_tahun ?? '-'}}</td>
                @endif
            @endforeach
            @foreach($data['kriteria'] as $key_kriteria => $val_kriteria)
                @if($key_kriteria == 1)
                    <td align="center">{{$val_kriteria ?? '-'}}</td>
                @endif
            @endforeach
            @foreach($data['kesimpulan'] as $key_kesimpulan => $val_kesimpulan)
                @if($key_kesimpulan == 1)
                    <td align="center">{{$val_kesimpulan ?? '-'}}</td>
                @endif
            @endforeach
        </tr>
        @foreach($data['tahun'] as $key_tahun => $val_tahun)
        @foreach($data['kriteria'] as $key_kriteria => $val_kriteria)
        @foreach($data['kesimpulan'] as $key_kesimpulan => $val_kesimpulan)
        @if($key_tahun == $key_kriteria && $key_tahun == $key_kesimpulan)
            @if($key_tahun != 1 && $key_kriteria != 1 && $key_kesimpulan != 1)
                <tr>
                    <td align="center">{{$val_tahun ?? '-'}}</td>
                    <td align="center">{{$val_kriteria ?? '-'}}</td>
                    <td>{{$val_kesimpulan ?? '-'}}</td>
                </tr>
            @endif
        @endif
        @endforeach
        @endforeach
        @endforeach
        <!-- <tr>
            <td>2022</td>
        </tr>
        <tr>
            <td>2021</td>
        </tr> -->
            <!-- <td align="center">
                @foreach($data['tahun'] as $key_tahun => $val_tahun)
                <ul>
                    <li>{{$val_tahun}}</li>
                </ul>
                @endforeach
            </td>
            <td align="center">
                @foreach($data['kriteria'] as $key_kriteria => $val_kriteria)
                <ul>
                    <li>{{$val_kriteria}}</li>
                </ul>
                @endforeach
            </td>
            <td align="center">
                @foreach($data['kesimpulan'] as $key_kesimpulan => $val_kesimpulan)
                <ul>
                    <li>{{$val_kesimpulan}}</li>
                </ul>
                @endforeach
            </td> -->
        <!-- </tr> -->
    @endforeach
    </tbody>
</table>