<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" style="direction: rtl;" />
    <title>صورتحساب رسمی مرکز آریتمی تهران</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'DejaVu Sans', 'XB Zar', Tahoma, sans-serif;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            background-color: #ffffff;
            margin: 0;
            padding: 10px;
            color: #1e293b;
            font-size: 12px;
            direction: rtl !important;
            text-align: right;
        }

        .invoice-box {
            width: 100%;
            margin: 0 auto;
            background: #ffffff;
            padding: 10px;
        }

        /* سربرگ فاکتور */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #cbd5e1;
            margin-bottom: 16px;
            padding-bottom: 10px;
        }

        .header-table td {
            border: none;
            padding: 4px 0;
            vertical-align: middle;
        }

        .logo-img {
            max-height: 55px;
            max-width: 110px;
            object-fit: contain;
        }

        .clinic-title h1 {
            margin: 0 0 4px 0;
            font-size: 18px;
            color: #0f172a;
        }

        .clinic-title p {
            margin: 0;
            font-size: 11px;
            color: #64748b;
        }

        .invoice-meta {
            text-align: left;
            font-size: 11px;
            line-height: 1.8;
            color: #334155;
            direction: rtl !important;
        }

        .ltr-num {
            direction: ltr;
            unicode-bidi: bidi-override;
            display: inline-block;
            font-family: 'DejaVu Sans', Tahoma, sans-serif;
        }

        /* کارت مشخصات بیمار */
        .patient-card-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            margin-bottom: 18px;
        }

        .patient-card-table td {
            border: none;
            padding: 10px 12px;
            font-size: 11px;
        }

        .patient-card-table span {
            color: #64748b;
            margin-left: 4px;
        }

        /* جدول خدمات */
        .items-table {
            width: 100%;
            direction: rtl !important;
            text-align: right;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            text-align: right;
            padding: 10px 8px;
            border: 1px solid #cbd5e1;
            font-size: 11px;
        }

        .items-table td {
            padding: 9px 8px;
            border: 1px solid #e2e8f0;
            font-size: 11px;
            text-align: right;
        }

        .text-center {
            text-align: center !important;
        }

        .text-left {
            text-align: left !important;
        }

        .text-right {
            text-align: right !important;
        }

        /* جدول مبالغ و جمع کل */
        .totals-container-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-bottom: 20px;
        }

        .totals-container-table td {
            border: none;
            padding: 0;
        }

        .totals-table {
            width: 320px;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 7px 10px;
            border: 1px solid #e2e8f0;
            font-size: 11px;
        }

        .totals-table tr.grand-total {
            background-color: #f8fafc;
            font-weight: bold;
            font-size: 12px;
            color: #0f172a;
        }

        /* بخش امضا و مهر پزشکان */
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-top: 20px;
        }

        .signatures-table td {
            border: none;
            vertical-align: top;
            padding: 8px;
            text-align: center;
        }

        /* فوتر رسمی کلینیک */
        .official-footer {
            margin-top: 25px;
            border-top: 2px solid #0f766e;
            padding-top: 10px;
            font-size: 11px;
            color: #334155;
            page-break-inside: avoid;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            border: none;
            padding: 2px 0;
            vertical-align: middle;
        }

        .print-btn-container {
            text-align: center;
            margin-bottom: 15px;
        }

        .print-btn {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            font-weight: bold;
        }

        .service-code-badge {
            font-weight: bold;
            color: #0f766e;
            background-color: #f0fdfa;
            padding: 2px 6px;
            border: 1px solid #ccfbf1;
            display: inline-block;
            font-size: 11px;
            direction: ltr;
            unicode-bidi: bidi-override;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .invoice-box {
                padding: 0;
                max-width: 100%;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body dir="rtl" style="direction: rtl; text-align: right;">
    @php
    // دریافت لوگو و تبدیل امن به Base64 برای DomPDF
    $logoBase64 = null;
    $possibleLogoPaths = [
    public_path('images/logo.png'),
    public_path('images/logo.jpg'),
    public_path('logo.png'),
    base_path('public/images/logo.png'),
    storage_path('app/public/logo.png'),
    storage_path('app/public/images/logo.png'),
    ];

    foreach ($possibleLogoPaths as $path) {
    if (file_exists($path)) {
    $ext = pathinfo($path, PATHINFO_EXTENSION);
    $imgContent = @file_get_contents($path);
    if ($imgContent) {
    $logoBase64 = 'data:image/' . ($ext === 'jpg' ? 'jpeg' : $ext) . ';base64,' . base64_encode($imgContent);
    break;
    }
    }
    }

    $resolveRowServiceCode = function($row) {
    $rawCode = $row['service_code']
    ?? $row['serviceCode']
    ?? $row['code']
    ?? $row['service_id']
    ?? null;

    if ($rawCode !== null && $rawCode !== '' && $rawCode !== '—' && $rawCode !== '---') {
    return (string)$rawCode;
    }

    $title = mb_strtolower(trim($row['title'] ?? $row['serviceTitle'] ?? ''));

    $codesMap = [
    'اکوکاردیوگرافی' => '900120',
    'اکو' => '900120',
    'نوار قلب' => '900105',
    'الکتروکاردیوگرام' => '900105',
    'تست ورزش' => '900130',
    'هولتر ریتم' => '900140',
    'هولتر مانیتورینگ' => '900140',
    'هولتر فشار' => '900145',
    'ویزیت' => '900001',
    'مشاوره' => '900005',
    'سونوگرافی' => '700100',
    'آندوسکوپی' => '600110',
    'اسپیرومتری' => '800150',
    'آنژیوگرافی' => '900160',
    ];

    foreach ($codesMap as $name => $c) {
    if (mb_strpos($title, $name) !== false) {
    return $c;
    }
    }

    return '—';
    };
    @endphp

    <div class="print-btn-container no-print">
        <button class="print-btn" onclick="window.print()">🖨️ چاپ / ذخیره PDF</button>
    </div>

    <div class="invoice-box">
        <!-- سربرگ با لوگو -->
        <table class="header-table">
            <tr>
                <td style="width: 14%; text-align: right; vertical-align: middle;">
                    @if(!empty($logoBase64))
                    <img class="logo-img" src="{{ $logoBase64 }}" alt="لوگو مرکز آریتمی تهران" />
                    @elseif(file_exists(public_path('images/logo.png')))
                    <img class="logo-img" src="{{ public_path('images/logo.png') }}" alt="لوگو" />
                    @else
                    <img class="logo-img" src="{{ asset('images/logo.png') }}" onerror="this.style.display='none'" alt="لوگو" />
                    @endif
                </td>
                <td style="width: 46%; text-align: right; vertical-align: middle;">
                    <div class="clinic-title">
                        <h1>مرکز آریتمی تهران</h1>
                        <p>صورتحساب رسمی خدمات تشخیصی و درمانی</p>
                    </div>
                </td>
                <td style="width: 40%; text-align: left; vertical-align: middle;">
                    <div class="invoice-meta">
                        <div><strong>شماره پرونده:</strong> <span class="ltr-num">#{{ $archive->file_number ?? $archive->id ?? '—' }}</span></div>
                        <div><strong>کد رهگیری:</strong> <span class="ltr-num">{{ substr($archive->tracking_token ?? '', 0, 12) }}...</span></div>
                        <div><strong>تاریخ صدور:</strong> <span class="ltr-num">{{ $createdAt ?? '—' }}</span></div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- مشخصات بیمار -->
        <table class="patient-card-table">
            <tr>
                <td style="width: 36%; text-align: right;">
                    <span>بیمار:</span> <strong>{{ $patientName ?? '—' }}</strong>
                </td>
                <td style="width: 34%; text-align: center;">
                    <span>کد ملی:</span> <strong class="ltr-num">{{ $patientNationalCode ?? '—' }}</strong>
                </td>
                <td style="width: 30%; text-align: left;">
                    <span>شماره موبایل:</span> <strong class="ltr-num">{{ $patientMobile ?? '—' }}</strong>
                </td>
            </tr>
        </table>

        <!-- جدول اقلام خدمات -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 35px;">#</th>
                    <th class="text-center" style="width: 95px;">کد خدمت</th>
                    <th class="text-right">شرح خدمت / آزمایش</th>
                    <th class="text-right" style="width: 170px;">پزشک معالج / متخصص</th>
                    <th class="text-left" style="width: 130px;">مبلغ (تومان)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice['rows'] ?? [] as $row)
                @php
                $code = $resolveRowServiceCode($row);
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">
                        @if(!empty($code) && $code !== '—')
                        <span class="service-code-badge">{{ $code }}</span>
                        @else
                        <span style="color: #94a3b8;">—</span>
                        @endif
                    </td>
                    <td class="text-right"><strong>{{ $row['title'] ?? '—' }}</strong></td>
                    <td class="text-right">{{ $row['doctor'] ?? '—' }}</td>
                    <td class="text-left">
                        <span class="ltr-num">{{ isset($row['amount']) && is_numeric($row['amount']) ? number_format((int)$row['amount']) : '—' }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding: 20px; color: #94a3b8;">
                        هیچ ردیف خدمتی در این فاکتور یافت نشد.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- بخش جمع کل مبالغ -->
        <table class="totals-container-table">
            <tr>
                <td style="width: 50%;"></td>
                <td style="width: 50%;" align="left">
                    <table class="totals-table">
                        <tr>
                            <td style="text-align: right; width: 50%;">مجموع خدمات:</td>
                            <td style="text-align: left; width: 50%;"><strong class="ltr-num">{{ number_format($totalAmount ?? 0) }}</strong> تومان</td>
                        </tr>
                        @if(!empty($discount) && (float)$discount > 0)
                        <tr>
                            <td style="text-align: right; color: #dc2626;">تخفیف:</td>
                            <td style="text-align: left; color: #dc2626;"><span class="ltr-num">{{ number_format($discount) }}</span> تومان</td>
                        </tr>
                        @endif
                        <tr class="grand-total">
                            <td style="text-align: right;">مبلغ نهایی پرداختی:</td>
                            <td style="text-align: left; color: #16a34a;"><strong class="ltr-num">{{ number_format($payableAmount ?? 0) }}</strong> تومان</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- امضا و مهر پزشکان -->
        <table class="signatures-table">
            <tr>
                <td style="width: 40%;"></td>
                <td style="width: 60%;" align="left">
                    <table style="width: 100%; border-collapse: collapse; border: none;">
                        <tr>
                            @forelse($uniqueDoctors ?? [] as $doctor)
                            <td style="border: none; text-align: center; vertical-align: top; padding: 6px;">
                                <div style="font-weight: bold; margin-bottom: 5px; font-size: 12px; color: #0f172a;">
                                    {{ $doctor['name'] ?? '' }}
                                </div>
                                @if(!empty($doctor['specialty']))
                                <div style="font-size: 10px; color: #64748b; margin-bottom: 6px;">{{ $doctor['specialty'] }}</div>
                                @endif

                                @if(!empty($doctor['stamp_url']))
                                <img src="{{ $doctor['stamp_url'] }}" alt="مهر {{ $doctor['name'] ?? '' }}" style="max-height: 80px; max-width: 140px;" />
                                @else
                                <div style="color: #94a3b8; font-size: 10px; padding-top: 15px;">(محل مهر و امضای پزشک)</div>
                                @endif
                            </td>
                            @empty
                            <td style="border: none; text-align: left; color: #94a3b8; font-size: 11px;">
                                مهر پزشک ثبت نشده است.
                            </td>
                            @endforelse
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- فوتر رسمی با آدرس، تلفن و وب‌سایت مرکز -->
        <div class="official-footer">
            <table class="footer-table">
                <tr>
                    <td style="text-align: right; width: 62%;">
                        <div><strong>تهران، خیابان ولیعصر، خیابان توانیر، بالاتر از بیمارستان دی، ساختمان شماره ۵</strong></div>
                        <div style="margin-top: 3px;">تلفن: ۰۲۱ ۸۸۸۸۷۲۷۰</div>
                    </td>
                    <td style="text-align: left; width: 38%; direction: ltr; font-family: 'DejaVu Sans', Tahoma, sans-serif;">
                        <div style="font-weight: bold; color: #0f766e;">www.TehranEP.center</div>
                        <div style="margin-top: 3px; color: #475569;">Tel &amp; WhatsApp: +98(21) 88887270</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>