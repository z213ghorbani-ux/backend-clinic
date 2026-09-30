<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\SecretaryController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\ReportController;

/*
|--------------------------------------------------------------------------
| Public Portal and Authentication Routes
|--------------------------------------------------------------------------
*/

// Login with rate limiting
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1');

// Patient portal verification
Route::post('/portal/{token}/verify', [ArchiveController::class, 'portalVerify'])
    ->middleware('throttle:10,1');

// Public patient portal
Route::get('/portal/{token}', [ArchiveController::class, 'portalShow']);

Route::get('/portal/{token}/invoice-pdf', [
    ArchiveController::class,
    'portalInvoicePdf'
]);

Route::get('/portal/{token}/attachments/{index}', [
    ArchiveController::class,
    'portalDownloadAttachment'
])->whereNumber('index');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    /*
    |--------------------------------------------------------------------------
    | Doctors
    |--------------------------------------------------------------------------
    | مشاهده پزشکان برای همه کاربران احراز هویت‌شده مجاز است.
    */

    Route::get('/doctors', [DoctorController::class, 'index']);
    Route::get('/doctors/{doctor}', [DoctorController::class, 'show']);

    /*
    |--------------------------------------------------------------------------
    | Lab Drafts
    |--------------------------------------------------------------------------
    */

    Route::get('/lab-drafts', [
        ArchiveController::class,
        'draftIndex'
    ]);

    Route::post('/lab-drafts', [
        ArchiveController::class,
        'draftStore'
    ]);

    Route::delete('/lab-drafts/{id}', [
        ArchiveController::class,
        'draftDestroy'
    ])->whereNumber('id');

    // روت دریافت/دانلود فایل‌های پیش‌نویس برای ثبت نهایی و ساخت آرشیو
    Route::get('/lab-drafts/{id}/files/{fileIndex}', [
        ArchiveController::class,
        'draftDownloadFile'
    ])->whereNumber('id');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:admin')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Doctors Management
        |--------------------------------------------------------------------------
        */

        Route::post('/doctors', [
            DoctorController::class,
            'store'
        ]);

        Route::put('/doctors/{doctor}', [
            DoctorController::class,
            'update'
        ]);

        Route::delete('/doctors/{doctor}', [
            DoctorController::class,
            'destroy'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Secretaries Management
        |--------------------------------------------------------------------------
        */

        Route::apiResource(
            'secretaries',
            SecretaryController::class
        );

        /*
        |--------------------------------------------------------------------------
        | Reports
        |--------------------------------------------------------------------------
        */

        Route::get('/reports', [
            ReportController::class,
            'index'
        ]);

        Route::post('/reports/batch-delete', [
            ReportController::class,
            'batchDelete'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Archive Deletion
        |--------------------------------------------------------------------------
        */

        Route::delete('/archives/{archive}', [
            ArchiveController::class,
            'destroy'
        ]);

        Route::post('/archives/bulk-delete', [
            ArchiveController::class,
            'bulkDelete'
        ]);
    });

    /*
    |--------------------------------------------------------------------------
    | Archives
    |--------------------------------------------------------------------------
    | مجاز برای ادمین و منشی سطح یک
    */

    Route::middleware('role:admin,staff_level_1')->group(function () {

        Route::get('/archives', [
            ArchiveController::class,
            'index'
        ]);

        Route::post('/archives', [
            ArchiveController::class,
            'store'
        ]);

        Route::get('/archives/{archive}', [
            ArchiveController::class,
            'show'
        ]);

        Route::get('/archives/{archive}/attachments/{index}', [
            ArchiveController::class,
            'downloadAttachment'
        ])->whereNumber('index');
    });

    /*
    |--------------------------------------------------------------------------
    | Invoices & Visits
    |--------------------------------------------------------------------------
    | استعلام وضعیت، مشاهده و دریافت PDF برای همه کاربران لاگین‌شده
    | ایجاد، ویرایش، حذف و تسویه برای ادمین و منشی سطح یک
    |--------------------------------------------------------------------------
    */

    // استعلام وضعیت پرونده/ویزیت باز بیمار
    Route::get('/visits/status', [
        InvoiceController::class,
        'visitStatus'
    ]);

    Route::get('/invoices', [
        InvoiceController::class,
        'index'
    ]);

    Route::get('/invoices/{invoice}', [
        InvoiceController::class,
        'show'
    ]);

    // صدور خروجی PDF فاکتور رسمی
    Route::get('/invoices/{invoice}/official-pdf', [
        InvoiceController::class,
        'officialPdf'
    ]);

    Route::middleware('role:admin,staff_level_1')->group(function () {

        Route::post('/invoices', [
            InvoiceController::class,
            'store'
        ]);

        Route::put('/invoices/{invoice}', [
            InvoiceController::class,
            'update'
        ]);

        // تسویه فاکتور
        Route::post('/invoices/{invoice}/pay', [
            InvoiceController::class,
            'pay'
        ]);

        Route::delete('/invoices/{invoice}', [
            InvoiceController::class,
            'destroy'
        ]);
    });

    /*
    |--------------------------------------------------------------------------
    | Patients
    |--------------------------------------------------------------------------
    */

    Route::apiResource(
        'patients',
        PatientController::class
    )->except(['destroy']);

    // حذف بیمار فقط توسط ادمین
    Route::delete(
        'patients/{patient}',
        [PatientController::class, 'destroy']
    )->middleware('role:admin');

    /*
    |--------------------------------------------------------------------------
    | Services and Tariffs
    |--------------------------------------------------------------------------
    */

    Route::patch(
        'services/{service}/toggle-status',
        [ServiceController::class, 'toggleStatus']
    )->whereNumber('service');

    Route::apiResource(
        'services',
        ServiceController::class
    );

    /*
    |--------------------------------------------------------------------------
    | Appointments
    |--------------------------------------------------------------------------
    */

    Route::apiResource(
        'appointments',
        AppointmentController::class
    );

    /*
    |--------------------------------------------------------------------------
    | Resend Patient Portal SMS
    |--------------------------------------------------------------------------
    */

    Route::post('/archives/{id}/notify', [
        ArchiveController::class,
        'notifyPatient'
    ])->middleware('throttle:5,1');
});
