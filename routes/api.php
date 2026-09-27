<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\SecretaryController;

// -------------------------------------------------------------
// روت‌های عمومی پرتال بیمار و احراز هویت (همراه با Rate Limiting)
// -------------------------------------------------------------
// ضد Brute-Force: حداکثر ۵ تلاش در هر دقیقه
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1');

// ضد اسپم و حدس کد وریفای پورتال: حداکثر ۱۰ درخواست در هر دقیقه
Route::post('/portal/{token}/verify', [ArchiveController::class, 'portalVerify'])
    ->middleware('throttle:10,1');

Route::get('/portal/{token}', [ArchiveController::class, 'portalShow']);
Route::get('/portal/{token}/invoice-pdf', [ArchiveController::class, 'portalInvoicePdf']);
Route::get('/portal/{token}/attachments/{index}', [ArchiveController::class, 'portalDownloadAttachment'])
    ->whereNumber('index');

// -------------------------------------------------------------
// روت‌های نیازمند احراز هویت (Sanctum)
// -------------------------------------------------------------
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // لیست پزشکان برای همه کاربران احراز هویت شده باز است
    Route::get('/doctors', [DoctorController::class, 'index']);
    Route::get('/doctors/{doctor}', [DoctorController::class, 'show']);

    // پیش‌نویس جوابدهی (صف موقت) - برای همه کاربران احراز هویت‌شده
    Route::get('/lab-drafts', [ArchiveController::class, 'draftIndex']);
    Route::post('/lab-drafts', [ArchiveController::class, 'draftStore']);
    Route::get('/lab-drafts/{id}/files/{index}', [ArchiveController::class, 'draftFile'])
        ->whereNumber(['id', 'index']);
    Route::delete('/lab-drafts/{id}', [ArchiveController::class, 'draftDestroy'])
        ->whereNumber('id');

    // =============================================================
    // عملیات اختصاصی فقط ادمین (تعریف پزشک، منشی، گزارشات سیستمی و حذف بایگانی)
    // =============================================================
    Route::middleware('role:admin')->group(function () {
        Route::post('/doctors', [DoctorController::class, 'store']);
        Route::put('/doctors/{doctor}', [DoctorController::class, 'update']);
        Route::delete('/doctors/{doctor}', [DoctorController::class, 'destroy']);

        Route::apiResource('secretaries', SecretaryController::class);

        Route::get('/reports', [ReportController::class, 'index']);
        Route::post('/reports/batch-delete', [ReportController::class, 'batchDelete']);

        // عملیات حذف سوابق بایگانی مختص ادمین باقی می‌ماند
        Route::delete('/archives/{archive}', [ArchiveController::class, 'destroy']);
        Route::post('/archives/bulk-delete', [ArchiveController::class, 'bulkDelete']);
    });

    // =============================================================
    // ثبت نهایی و صدور جوابدهی + مشاهده آرشیو و دانلود پیوست‌ها
    // مجاز برای: ادمین و منشی‌های سطح یک (staff_level_1)
    // =============================================================
    Route::middleware('role:admin,staff_level_1')->group(function () {
        Route::get('/archives', [ArchiveController::class, 'index']);
        Route::post('/archives', [ArchiveController::class, 'store']); // ثبت نهایی، ممهور شدن و ارسال پیامک
        Route::get('/archives/{archive}', [ArchiveController::class, 'show']);
        Route::get('/archives/{archive}/attachments/{index}', [ArchiveController::class, 'downloadAttachment'])
            ->whereNumber('index');
    });

    // -------------------------------------------------------------
    // فاکتور (Invoices)
    // مشاهده: همه کاربران لاگین‌شده
    // ایجاد/ویرایش/حذف: ادمین و منشی سطح ۱ (سطح ۲ دسترسی ندارد)
    // -------------------------------------------------------------
    Route::get('/invoices', [InvoiceController::class, 'index']);
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show']);

    Route::middleware('role:admin,staff_level_1')->group(function () {
        Route::post('/invoices', [InvoiceController::class, 'store']);
        Route::put('/invoices/{invoice}', [InvoiceController::class, 'update']);
        Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy']);
    });

    // بیماران، خدمات و نوبت‌ها
    Route::apiResource('patients', PatientController::class)->except(['destroy']);
    Route::delete('patients/{patient}', [PatientController::class, 'destroy'])->middleware('role:admin');

    Route::apiResource('services', ServiceController::class);
    Route::apiResource('appointments', AppointmentController::class);

    // ارسال مجدد پیامک لینک پرتال (حداکثر ۵ بار در دقیقه)
    Route::post('/archives/{id}/notify', [ArchiveController::class, 'notifyPatient'])
        ->middleware('throttle:5,1');
});
