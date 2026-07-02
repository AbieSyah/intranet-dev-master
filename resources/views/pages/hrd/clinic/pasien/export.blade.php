<table>
    <thead>
        <tr>
            <th align="center"><b>No</b></th>
            <th align="center"><b>Date</b></th>
            <th align="center"><b>Patient Name</b></th>
            <th align="center"><b>Diagnose</b></th>
            <th align="center"><b>Symptoms</b></th>
            <th align="center"><b>Tension</b></th>
            <th align="center"><b>Keterangan</b></th>
            <th align="center"><b>Doctor Name</b></th>
            <th align="center"><b>Penggunaan Obat</b></th>
        </tr>
    </thead>
    <tbody>
    @foreach($query as $data)
        <tr>
            <td align="center">{{$data['no']}}</td>
            <td align="center">{{$data['visit_date']}}</td>
            <td align="center">{{$data['id_employee']}}</td>
            <td align="center">{{$data['diagnosa']}}</td>
            <td align="center">{{$data['keluhan']}}</td>
            <td align="center">{{$data['tensi']}}</td>
            <td align="center">{{$data['keterangan']}}</td>
            <td align="center">{{$data['doctor']}}</td>
            <td align="center">{{$data['obat']}}</td>
        </tr>       
    @endforeach
    </tbody>
</table>