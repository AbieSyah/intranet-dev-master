@extends('layouts.codeqr')
@section('link')
@endsection
@section('content')
    <div class="col-12">
        <table class="body-wrap" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: transparent; margin: 0;">
            <tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                <td style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
                <td class="container" width="600" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
                    <div class="content" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
                        <table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; margin: 0; border: none;">
                            <tr style="font-family: 'Roboto', sans-serif; font-size: 14px; margin: 0;">
                                <td class="content-wrap" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; color: #495057; font-size: 14px; vertical-align: top; margin: 0;padding: 30px; box-shadow: 0 3px 15px rgba(30,32,37,.06); ;border-radius: 7px; background-color: #fff;" valign="top">
                                    <meta itemprop="name" content="Confirm Email" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" />
                                    <table width="100%" cellpadding="0" cellspacing="0" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                        <tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                            <td class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 24px; vertical-align: top; margin: 0; padding: 0 0 10px; text-align: center;" valign="top">
                                                <!-- <h4 style="font-family: 'Roboto', sans-serif; margin-bottom: 10px; font-weight: 600;"><span class="badge bg-success ms-1"><i class="ri-checkbox-circle-line" style="vertical-align: middle;"></i> Verified Direktur Produksi</span></h4> -->
                                                <!-- <div class="text-center"> -->

                                                    <div class="avatar-md mt-5 mb-4 mx-auto">
                                                        <div class="avatar-title bg-light text-success display-4 rounded-circle">
                                                            <i class="ri-checkbox-circle-fill"></i>
                                                        </div>
                                                    </div>
                                                    <h4 style="font-family: 'Roboto', sans-serif; margin-bottom: 10px; font-weight: 600;">Approved !</h4>
                                                <!-- </div> -->
                                            </td>
                                        </tr>
                                        <tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                            <td class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 12px;" valign="top">
                                                <h5 style="font-family: 'Roboto', sans-serif; margin-bottom: 3px;">Peserta Training :</h5>
                                                <p style="font-family: 'Roboto', sans-serif; margin-bottom: 8px; color: #878a99;">{{$query->fkt->peserta->fullname ?? '-'}}</p>
                                            </td>
                                        </tr>
                                        <tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                            <td class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 12px;" valign="top">
                                                <h5 style="font-family: 'Roboto', sans-serif; margin-bottom: 3px;">Usulan Topik Training :</h5>
                                                <p style="font-family: 'Roboto', sans-serif; margin-bottom: 8px; color: #878a99;">{{$query->fkt->judul ?? '-'}}</p>
                                            </td>
                                        </tr>
                                        <tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                            <td class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 12px;" valign="top">
                                                <h5 style="font-family: 'Roboto', sans-serif; margin-bottom: 3px;">Disetujui Oleh :</h5>
                                                @if($query->type == 1)
                                                <p style="font-family: 'Roboto', sans-serif; margin-bottom: 8px; color: #878a99;">{{$query->fkt->fpkt->peserta->fullname ?? '-'}}</p>
                                                @elseif($query->type == 2)
                                                <p style="font-family: 'Roboto', sans-serif; margin-bottom: 8px; color: #878a99;">{{$query->fkt->fpkt->atasan->fullname ?? '-'}}</p>
                                                @else
                                                <p style="font-family: 'Roboto', sans-serif; margin-bottom: 8px; color: #878a99;">{{$query->fkt->fpkt->hrd->fullname ?? '-'}}</p>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                            <td class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 12px;" valign="top">
                                                <h5 style="font-family: 'Roboto', sans-serif; margin-bottom: 3px;">Tanggal Disetujui :</h5>
                                                <p style="font-family: 'Roboto', sans-serif; margin-bottom: 8px; color: #878a99;">{{$query->date_approval ?? '-'}}</p>
                                            </td>
                                        </tr>
                                        <tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                            <td class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 18px;" valign="top">
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <div style="text-align: center; margin: 28px auto 0px auto;">
                            <p style="font-family: 'Roboto', sans-serif; font-size: 14px;color: #98a6ad; margin: 0px;"><script>document.write(new Date().getFullYear())</script> © PT Hisamitsu Pharma Indonesia. Design & Develop Information & Technology</p>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <!-- end table -->
    </div>
    <!--end col-->    
@endsection
@section('script')
@endsection
