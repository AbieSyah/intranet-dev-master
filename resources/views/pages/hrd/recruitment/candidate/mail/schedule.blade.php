<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - PT. Hisamitsu Pharma Indonesia</title>
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; }
        a { text-decoration: none; }
        .primary-color { color: #003DA7; }
        .bg-primary { background-color: #003DA7; }
        .button { background-color: #00A74F; color: #ffffff; padding: 10px 20px; border-radius: 5px; display: inline-block; font-weight: bold; }
        .wrapper { max-width: 600px; margin: 0 auto; padding: 20px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background-color: #003DA7; padding: 20px 0; border-top-left-radius: 8px; border-top-right-radius: 8px; text-align: center; }
        .content { padding: 30px; line-height: 1.6; color: #333333; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #999999; border-top: 1px solid #eeeeee; }
        .data-table td { padding: 8px 0; border-bottom: 1px solid #eeeeee; }
        .data-table strong { color: #003DA7; }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td align="center" style="padding: 20px 0;">
            <table class="wrapper" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td class="header" style="padding: 20px 30px;">
                        <img src="https://intranet.hisamitsu.co.id/assets/images/hisamitsu.png" alt="PT. Hisamitsu Pharma Indonesia" style="height: 60px; display: block; margin: 0 auto; border: 0;">
                    </td>
                </tr>
                <tr>
                    <td class="content">
                        <h2 class="primary-color" style="margin-top: 0;">{{ $title }}</h2>
                        <p>Dear <b>{{ $candidate['nickname'] }}</b>,</p>
                        <p>Congratulations! We are pleased to inform you that you have <strong>Passed</strong> the previous stage of our recruitment process.</p>
                        <p>As the next step, we would like to invite you to participate in the upcoming <strong>{{ $selection ?? 'Selection' }}</strong> session.</p>
                        <p>Please find the schedule and details below:</p>
                        <table class="data-table" border="0" cellpadding="0" cellspacing="0" width="100%"
                            style="margin-bottom: 25px;">
                            <tr>
                                <td style="width: 100px; padding: 8px 0; border-bottom: 1px solid #eeeeee;">
                                    <strong>Location</strong></td>
                                <td style="padding: 8px 0; border-bottom: 1px solid #eeeeee; width: 10px;">:</td>
                                <td style="padding: 8px 0; border-bottom: 1px solid #eeeeee;">
                                    {{ $location ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="width: 100px; padding: 8px 0; border-bottom: 1px solid #eeeeee;">
                                    <strong>Schedule</strong></td>
                                <td style="padding: 8px 0; border-bottom: 1px solid #eeeeee; width: 10px;">:</td>
                                <td style="padding: 8px 0; border-bottom: 1px solid #eeeeee;">
                                    {{ \Carbon\Carbon::parse($schedule)->isoFormat('dddd, D MMMM YYYY - HH:mm') }}
                                    WIB</td>
                            </tr>
                            <tr>
                                <td style="width: 100px; padding: 8px 0; border-bottom: 1px solid #eeeeee;">
                                    <strong>KTP Number</strong></td>
                                <td style="padding: 8px 0; border-bottom: 1px solid #eeeeee; width: 10px;">:</td>
                                <td style="padding: 8px 0; border-bottom: 1px solid #eeeeee;">
                                    {{ $candidate['no_ktp'] }}</td>
                            </tr>
                        </table>
                        <p style="font-style: italic; color: #555555; text-align: center; margin-top: 20px;">
                            Please ensure you arrive on time and bring any necessary documents. We look forward to meeting you!
                        </p>
                        <p>Sincerely,</p>
                        <p style="font-weight: bold; margin-bottom: 0;">Recruitment Team</p>
                        <p style="margin-top: 5px;" class="primary-color">PT. Hisamitsu Pharma Indonesia</p>
                    </td>
                </tr>
                <tr>
                    <td class="footer">
                        <p>{{ date('Y') }} &copy; PT. Hisamitsu Pharma Indonesia</p>
                        <p>This is an automated email, please do not reply.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>