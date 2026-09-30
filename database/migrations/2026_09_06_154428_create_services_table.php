<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('services');
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('services')->nullOnDelete();
            $table->string('name');                          // نام خدمت یا دسته
            $table->string('code')->nullable()->unique();    // کد خدمت برای فاکتور
            $table->unsignedBigInteger('price')->default(0); // تعرفه (تومان/ریال)
            $table->boolean('is_active')->default(true);     // وضعیت فعال/غیرفعال
            $table->boolean('is_visit')->default(false);     // آیا از جنس ویزیت است؟
            $table->integer('sort_order')->default(0);       // ترتیب نمایش
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
