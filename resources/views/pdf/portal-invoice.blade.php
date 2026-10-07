<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>صورتحساب رسمی مرکز آریتمی تهران</title>
    <style>
        body {
            font-family: 'dejavusans', 'vazir', 'tahoma', sans-serif;
            font-size: 11px;
            color: #1a202c;
            direction: rtl;
            text-align: right;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .page-banner {
            background-color: #e8edf3;
            color: #2d3748;
            font-size: 10px;
            font-weight: bold;
            padding: 6px 12px;
            margin-bottom: 14px;
        }

        .head-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #cbd5e0;
        }

        .head-table td {
            padding: 4px 6px 8px 6px;
            vertical-align: middle;
        }

        .head-table td img {
            display: block;
        }

        .clinic-title {
            font-size: 20px;
            font-weight: bold;
            color: #1a365d;
        }

        .clinic-sub {
            font-size: 10.5px;
            color: #4a5568;
        }

        .meta {
            font-size: 10px;
            color: #2d3748;
            text-align: left;
            line-height: 1.7;
        }

        .patient-bar {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #cbd5e0;
            background-color: #f7fafc;
            margin-top: 14px;
        }

        .patient-bar td {
            padding: 9px 12px;
            font-size: 11px;
        }

        .cert-box {
            border: 1px dashed #a0aec0;
            background-color: #f8fafc;
            padding: 14px 16px;
            margin-top: 16px;
        }

        .cert-title {
            color: #0f766e;
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 8px;
        }

        .cert-text {
            font-size: 11px;
            color: #2d3748;
            line-height: 2;
            text-align: justify;
        }

        .doctors-title {
            font-weight: bold;
            font-size: 12px;
            color: #2d3748;
            border-bottom: 1px solid #cbd5e0;
            padding-bottom: 6px;
            margin-top: 34px;
        }

        .doctors-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }

        .doctors-table td {
            text-align: center;
            vertical-align: bottom;
            padding: 6px;
        }

        .doc-name {
            font-weight: bold;
            font-size: 11.5px;
            color: #1a202c;
            margin-top: 4px;
        }

        .items-title {
            font-size: 15px;
            font-weight: bold;
            color: #1a365d;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }

        .items-table th {
            background-color: #edf2f7;
            color: #2d3748;
            border: 1px solid #cbd5e0;
            padding: 8px 8px;
            font-size: 10.5px;
            font-weight: bold;
            text-align: center;
        }

        .items-table td {
            border: 1px solid #cbd5e0;
            padding: 8px 8px;
            font-size: 10.5px;
            text-align: center;
        }

        .sum-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sum-table td {
            border: 1px solid #cbd5e0;
            padding: 8px 10px;
            font-size: 11px;
        }

        .footer-table {
            width: 100%;
            border-top: 2px solid #0f766e;
            border-collapse: collapse;
        }

        .footer-table td {
            padding-top: 6px;
            font-size: 10px;
            color: #2d3748;
            vertical-align: top;
        }
    </style>
</head>

<body>

    @php
    $visitDoctors = $visitDoctors ?? [];
    $hasPrescription = trim((string) ($doctorPrescriptionText ?? '')) !== '';
    @endphp

    <!-- فوتر مشترک هر دو صفحه -->
    <htmlpagefooter name="clinicFooter">
        <table class="footer-table">
            <tr>
                <td style="width: 60%; text-align: right;">
                    <strong>تهران، خیابان ولیعصر، خیابان توانیر، بالاتر از بیمارستان دی، ساختمان شماره ۵</strong><br>
                    تلفن: {{ fa_num('021 88887270') }}
                </td>
                <td style="width: 40%; text-align: left; direction: ltr;">
                    <strong style="color: #0f766e; font-size: 12px;">www.TehranEP.center</strong><br>
                    Tel &amp; WhatsApp: +98(21) 88887270
                </td>
            </tr>
        </table>
    </htmlpagefooter>
    <sethtmlpagefooter name="clinicFooter" value="on" />

    <!-- ===================== صفحه ۱ ===================== -->
    <div class="page-banner">صفحه ۱ از ۲ (اطلاعات پرونده و تاییدیه پزشک)</div>

    <table class="head-table">
        <tr>
            @if(!empty($logo))
            <td style="width: 18%; text-align: left; padding-left: 10px;">
                <img src="{{ $logo }}" alt="لوگو" style="height: 62px; width: auto;">
            </td>
            @endif
            <td style="text-align: right;">
                <div class="clinic-title">مرکز آریتمی تهران</div>
                <div class="clinic-sub">صورتحساب رسمی خدمات تشخیصی و درمانی</div>
            </td>
            <td class="meta" style="width: 30%;">
                <div><strong>شماره پرونده / فاکتور:</strong> #{{ fa_num($invoice['file_number']) }}</div>
                <div><strong>کد رهگیری:</strong> {{ fa_num($invoice['tracking_code']) }}</div>
                <div><strong>تاریخ صدور:</strong> {{ fa_num($invoice['issued_at']) }}</div>
            </td>
        </tr>
    </table>

    <table class="patient-bar">
        <tr>
            <td><strong>بیمار:</strong> {{ $patient['name'] }}</td>
            <td><strong>کد ملی:</strong> {{ fa_num($patient['national_code']) }}</td>
            <td><strong>شماره موبایل:</strong> {{ fa_num($patient['phone']) }}</td>
        </tr>
    </table>

    <div class="cert-box">
        <div class="cert-text">
            {{ $cert['title'] }} <strong>{{ $cert['name'] }}</strong>
            با کد ملی <strong>{{ fa_num($cert['national_code']) }}</strong>
            در تاریخ <strong>{{ fa_num($cert['date']) }}</strong>
            به مرکز آریتمی تهران مراجعه نموده و خدمات
            <strong>{{ $cert['services'] }}</strong>
            برای ایشان ثبت و انجام شده است.
            @if($hasPrescription)
            <br>{!! nl2br(e($doctorPrescriptionText)) !!}
            @endif
        </div>
    </div>

    <div class="doctors-title">مهر و امضای پزشک:</div>

    <table class="doctors-table">
        <tr>
            @forelse($visitDoctors as $doc)
            <td>
                @if(!empty($doc['stamp']))
                <img src="{{ $doc['stamp'] }}" alt="مهر پزشک" style="max-height: 120px; max-width: 220px;">
                @else
                <div style="height: 90px;"></div>
                @endif
                <div class="doc-name">{{ $doc['name'] }}</div>
            </td>
            @empty
            <td>
                <div style="height: 90px;"></div>
            </td>
            @endforelse
        </tr>
    </table>

    <pagebreak />

    <!-- ===================== صفحه ۲ ===================== -->
    <div class="page-banner">صفحه ۲ از ۲ (ریز اقلام خدمات و تسویه حساب)</div>

    <table class="head-table">
        <tr>
            <td>
                <div class="items-title">صورتحساب درمانی</div>
                <div style="font-size: 10.5px; color: #4a5568;">
                    بیمار: <strong>{{ $patient['name'] }}</strong> &nbsp;|&nbsp; کد ملی: {{ fa_num($patient['national_code']) }}
                </div>
            </td>
            <td class="meta" style="width: 32%;">
                <div><strong>شماره پرونده / فاکتور:</strong> #{{ fa_num($invoice['file_number']) }}</div>
                <div><strong>شماره فاکتور:</strong> {{ fa_num($invoice['invoice_number']) }}</div>
                <div><strong>تاریخ صدور:</strong> {{ fa_num($invoice['issued_at']) }}</div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 6%;">#</th>
                <th style="width: 14%;">کد خدمت</th>
                <th style="width: 40%;">شرح خدمت / آزمایش</th>
                <th style="width: 22%;">پزشک معالج / متخصص</th>
                <th style="width: 18%;">مبلغ (تومان)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $index => $row)
            <tr>
                <td>{{ fa_num($index + 1) }}</td>
                <td>{{ fa_num($row['code']) }}</td>
                <td style="text-align: right;">
                    @if(!empty($row['parent_name']))
                    <div style="font-size: 8.5px; color: #718096;">{{ $row['parent_name'] }}</div>
                    @endif
                    <strong>{{ $row['service_name'] }}</strong>
                </td>
                <td>{{ $row['doctor_name'] ?? '---' }}</td>
                <td>{{ fa_num($row['amount'], true) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="padding: 15px; color: #a0aec0;">هیچ ردیف خدمتی ثبت نشده است.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table style="width: 100%; margin-top: 16px; border-collapse: collapse;">
        <tr>
            <td style="width: 52%;"></td>
            <td style="width: 48%; vertical-align: top;">
                <table class="sum-table">
                    <tr>
                        <td style="color: #4a5568;">مجموع خدمات:</td>
                        <td style="text-align: left;"><strong>{{ fa_num($invoice['total_amount'], true) }}</strong> تومان</td>
                    </tr>
                    @if(($invoice['discount'] ?? 0) > 0)
                    <tr>
                        <td style="color: #e53e3e;">تخفیف:</td>
                        <td style="text-align: left; color: #e53e3e;">{{ fa_num($invoice['discount'], true) }} تومان</td>
                    </tr>
                    @endif
                    @if(($invoice['insurance_share'] ?? 0) > 0)
                    <tr>
                        <td style="color: #2b6cb0;">سهم بیمه:</td>
                        <td style="text-align: left; color: #2b6cb0;">{{ fa_num($invoice['insurance_share'], true) }} تومان</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="color: #4a5568;">مبلغ نهایی پرداختی:</td>
                        <td style="text-align: left; color: #15803d;"><strong>{{ fa_num($invoice['payable_amount'], true) }}</strong> تومان</td>
                    </tr>
                    @if(!empty($invoice['payment_method']))
                    <tr>
                        <td style="color: #4a5568;">روش پرداخت:</td>
                        <td style="text-align: left;">{{ $invoice['payment_method'] }}</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <div class="doctors-title" style="margin-top: 26px;">مهر و امضای پزشک:</div>

    <table class="doctors-table">
        <tr>
            @forelse($visitDoctors as $doc)
            <td>
                @if(!empty($doc['stamp']))
                <img src="{{ $doc['stamp'] }}" alt="مهر پزشک" style="max-height: 110px; max-width: 200px;">
                @else
                <div style="height: 80px;"></div>
                @endif
                <div class="doc-name">{{ $doc['name'] }}</div>
            </td>
            @empty
            <td>
                <div style="height: 80px;"></div>
            </td>
            @endforelse
        </tr>
    </table>

</body>

</html>