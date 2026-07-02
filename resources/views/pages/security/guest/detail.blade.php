@extends('layouts.master')
@section('link')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
    <x-page-title title="Guest Details" :breadcrumbs="['Employee', 'Security Form']" />

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex justify-content-between">
                    <h3>Detail Tamu</h3>

                    <a href="{{ route('guest.index') }}" class="float-end btn btn-primary btn-label waves-effect waves-light">
                        <i class="mdi mdi-arrow-left label-icon align-middle fs-16 me-2"></i>Back
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-borderless">
                        <tbody>
                            <tr>
                                <th style="width: 30%">Tanggal Kunjungan</th>
                                <td style="width: 70%">{{ $guestForm->created_at->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>Nama Tamu</th>
                                <td>{{ $guestForm->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Alamat Pribadi</th>
                                <td>{{ $guestForm->alamat_pribadi ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>ID Kartu</th>
                                <td>{{ $guestForm->nomor_kartu_identitas ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Nama Perusahaan</th>
                                <td>{{ $guestForm->perusahaan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Tujuan Kunjungan</th>
                                <td>{{ $guestForm->tujuan_kunjungan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Lama Kunjungan</th>
                                <td>{{ $guestForm->lama_kunjungan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>PIC Tujuan</th>
                                <td>{{ $guestForm->nama_pic ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Suhu Badan</th>
                                <td>{{ $guestForm->suhu ?? '-' }} °C</td>
                            </tr>
                            <tr>
                                <th>Resiko Kesehatan</th>
                                <td>{{ $guestForm->resiko_kesehatan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>No Visitor</th>
                                <td>{{ $guestForm->nomor_visitor ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Jenis Kendaraan</th>
                                <td>{{ $guestForm->jenis_kendaraan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Nopol</th>
                                <td>{{ $guestForm->nomor_polisi ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Barang yang dibawa keluar / masuk</th>
                                <td>{{ $guestForm->muatan_kendaraan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Jam Masuk</th>
                                <td>{{ $guestForm->created_at?->format('H:i') ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Jam Bertemu</th>
                                <td>{{ $guestForm->waktu_bertemu?->format('H:i') ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Jam Keluar</th>
                                <td>{{ $guestForm->waktu_keluar?->format('H:i') ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Foto Kartu Identitas</th>
                                <td>
                                    @php
                                        $filePath = 'tamu/' . $guestForm->id . '.jpg'; // Path to the file
                                    @endphp

                                    @if (Storage::disk('public')->exists($filePath))
                                        <img src="{{ Storage::disk('public')->url($filePath) }}" style="height: 250px;" />
                                    @else
                                        <p>File does not exist.</p>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
