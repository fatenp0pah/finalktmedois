<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id('StaffID');
            $table->unsignedBigInteger('UserID');
            $table->string('StaffName', 100);
            $table->string('StaffEmail', 100)->nullable();
            $table->string('StaffPhoneNum', 20)->nullable();
            $table->enum('StaffRole', ['Officer', 'Finance', 'Admin'])->default('Officer');
            $table->string('Department', 50)->nullable();
            $table->timestamps();
            $table->foreign('UserID')->references('UserID')->on('users')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
