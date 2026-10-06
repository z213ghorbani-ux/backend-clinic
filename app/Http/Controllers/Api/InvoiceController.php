<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Requests\PayInvoiceRequest;
use App\Models\Appointment;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * لیست فاکتورها همراه با صفحه‌بندی و روابط
     */
    public function index(Request $request): JsonResponse
    {
        $query = Invoice::with(['appointment.doctor', 'appointment.service', 'patient']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        $invoices = $query->latest()->paginate(15);

        return response()->json($invoices);
    }

    /**
     * ثبت فاکتور جدید
     */
    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $appointment = Appointment::findOrFail($validated['appointment_id']);

        $amount = (int) $validated['amount'];
        $discount = (int) ($validated['discount'] ?? 0);
        $finalAmount = max(0, $amount - $discount);
        $status = $validated['status'] ?? 'unpaid';

        $invoice = DB::transaction(function () use ($appointment, $validated, $amount, $discount, $finalAmount, $status) {
            return Invoice::create([
                'appointment_id' => $appointment->id,
                'patient_id'     => $appointment->patient_id,
                'amount'         => $amount,
                'discount'       => $discount,
                'final_amount'   => $finalAmount,
                'status'         => $status,
                'payment_method' => $validated['payment_method'] ?? null,
                'paid_at'        => $status === 'paid' ? now() : null,
                'notes'          => $validated['notes'] ?? null,
            ]);
        });

        return response()->json([
            'message' => 'فاکتور با موفقیت صادر شد.',
            'data'    => $invoice->load(['appointment.doctor', 'appointment.service', 'patient']),
        ], 201);
    }

    /**
     * نمایش جزئیات یک فاکتور
     */
    public function show(Invoice $invoice): JsonResponse
    {
        return response()->json(
            $invoice->load(['appointment.doctor', 'appointment.service', 'patient'])
        );
    }

    /**
     * ویرایش فاکتور
     */
    public function update(UpdateInvoiceRequest $request, Invoice $invoice): JsonResponse
    {
        $validated = $request->validated();

        $amount = array_key_exists('amount', $validated) ? (int) $validated['amount'] : $invoice->amount;
        $discount = array_key_exists('discount', $validated) ? (int) ($validated['discount'] ?? 0) : $invoice->discount;
        $finalAmount = max(0, $amount - $discount);

        $status = $validated['status'] ?? $invoice->status;

        $paidAt = $invoice->paid_at;
        if ($status === 'paid' && !$paidAt) {
            $paidAt = now();
        } elseif ($status !== 'paid') {
            $paidAt = null;
        }

        $invoice->update([
            ...$validated,
            'amount'       => $amount,
            'discount'     => $discount,
            'final_amount' => $finalAmount,
            'status'       => $status,
            'paid_at'      => $paidAt,
        ]);

        return response()->json([
            'message' => 'فاکتور با موفقیت بروزرسانی شد.',
            'data'    => $invoice->fresh()->load(['appointment.doctor', 'appointment.service', 'patient']),
        ]);
    }

    /**
     * حذف فاکتور
     */
    public function destroy(Invoice $invoice): JsonResponse
    {
        $invoice->delete();

        return response()->json([
            'message' => 'فاکتور با موفقیت حذف شد.',
        ]);
    }

    /**
     * تسویه فاکتور
     */
    public function pay(PayInvoiceRequest $request, Invoice $invoice): JsonResponse
    {
        if ($invoice->status === 'paid') {
            return response()->json([
                'message' => 'این فاکتور قبلاً پرداخت و تسویه شده است.',
            ], 422);
        }

        if ($invoice->status === 'cancelled') {
            return response()->json([
                'message' => 'فاکتور لغو شده قابل تسویه نیست.',
            ], 422);
        }

        $validated = $request->validated();

        $invoice->update([
            'status'         => 'paid',
            'payment_method' => $validated['payment_method'],
            'paid_at'        => $validated['paid_at'] ?? now(),
            'notes'          => $validated['notes'] ?? $invoice->notes,
        ]);

        return response()->json([
            'message' => 'فاکتور با موفقیت تسویه و پرداخت شد.',
            'data'    => $invoice->fresh()->load(['appointment.doctor', 'appointment.service', 'patient']),
        ]);
    }

    /**
     * خروجی PDF رسمی فاکتور
     */
    /**
     * خروجی PDF رسمی فاکتور
     */
    public function officialPdf(Invoice $invoice)
    {
        $invoice->load(['appointment.doctor', 'appointment.service', 'patient']);

        $patient = $invoice->patient;
        $appointment = $invoice->appointment;
        $doctor = $appointment?->doctor;
        $service = $appointment?->service;

        // آماده‌سازی لوگوی مرکز به صورت Base64
        $logoPath = public_path('images/logo.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        // آماده‌سازی مهر پزشک به صورت Base64 محلی برای جلوگیری از خطای ۴۰۳
        $stampBase64 = null;
        if ($doctor && !empty($doctor->stamp_path)) {
            $stampFullPath = storage_path('app/public/' . ltrim($doctor->stamp_path, '/'));
            if (!file_exists($stampFullPath)) {
                $stampFullPath = public_path('storage/' . ltrim($doctor->stamp_path, '/'));
            }

            if (file_exists($stampFullPath)) {
                $ext = pathinfo($stampFullPath, PATHINFO_EXTENSION);
                $stampBase64 = 'data:image/' . ($ext === 'jpg' ? 'jpeg' : $ext) . ';base64,' . base64_encode(file_get_contents($stampFullPath));
            }
        }

        $rows = [];
        if ($service) {
            $rows[] = [
                'code'   => $service->id ?? '—',
                'title'  => $service->name ?? 'خدمت پزشکی',
                'doctor' => $doctor ? ($doctor->first_name . ' ' . $doctor->last_name) : '—',
                'amount' => $invoice->amount,
            ];
        }

        $uniqueDoctors = [];
        if ($doctor) {
            $uniqueDoctors[] = [
                'name'      => 'دکتر ' . $doctor->first_name . ' ' . $doctor->last_name,
                'specialty' => $doctor->specialty ?? 'متخصص قلب و عروق',
                'stamp_url' => $stampBase64,
            ];
        }

        $data = [
            'archive'              => (object)[
                'file_number'    => $invoice->id,
                'tracking_token' => $invoice->id . '-' . ($appointment?->id ?? '0'),
            ],
            'createdAt'            => $invoice->created_at ? $invoice->created_at->format('Y/m/d') : now()->format('Y/m/d'),
            'patientName'          => $patient ? ($patient->first_name . ' ' . $patient->last_name) : '—',
            'patientNationalCode'  => $patient->national_code ?? '—',
            'patientMobile'        => $patient->mobile ?? '—',
            'invoice'              => ['rows' => $rows],
            'totalAmount'          => $invoice->amount,
            'discount'             => $invoice->discount,
            'payableAmount'        => $invoice->final_amount,
            'uniqueDoctors'        => $uniqueDoctors,
            'logoBase64'           => $logoBase64, // 🟢 ارسال لوگو به ویو
        ];

        // بارگذاری قالب رسمی
        $pdf = Pdf::loadView('pdf.portal-invoice', $data)
            ->setPaper('a4', 'portrait')
            ->setOption(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        return $pdf->stream("invoice-{$invoice->id}.pdf");
    }


    /**
     * بررسی وضعیت ویزیت یا فاکتور باز بیمار
     * بررسی هم‌زمان قفل بودن پرونده و وجود خدمت ویزیت پایه
     */
    public function visitStatus(Request $request): JsonResponse
    {
        $patientId = $request->query('patient_id');

        if (!$patientId) {
            return response()->json([
                'is_locked'           => false,
                'has_confirmed_visit' => false,
                'status'              => 'available',
                'message'             => 'شناسه بیمار ارسال نشده است.',
            ]);
        }

        // بررسی اینکه آیا بیمار فاکتور باز / تسویه نشده دارد یا خیر
        $hasPendingInvoice = Invoice::where('patient_id', $patientId)
            ->whereIn('status', ['unpaid', 'pending'])
            ->exists();

        // بررسی اینکه آیا بیمار حداقل یک فاکتور ویزیت تسویه شده دارد یا خیر
        $hasConfirmedVisit = Invoice::where('patient_id', $patientId)
            ->where('status', 'paid')
            ->whereHas('appointment.service', function ($query) {
                $query->where('is_visit', true);
            })
            ->exists();

        return response()->json([
            'is_locked'           => $hasPendingInvoice,
            'has_confirmed_visit' => $hasConfirmedVisit,
            'status'              => $hasPendingInvoice ? 'locked' : 'available',
        ]);
    }
}
