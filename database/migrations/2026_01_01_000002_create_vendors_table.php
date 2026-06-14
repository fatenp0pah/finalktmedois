<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id('VendorID');
            $table->unsignedBigInteger('UserID');
            $table->string('VendorNumber', 30)->unique();
            $table->string('CompanyName', 100);
            $table->string('RefNumber', 50)->nullable();
            $table->string('VendorEmail', 100)->nullable();
            $table->string('VendorContactNum', 20)->nullable();
            $table->string('ContactPerson', 100)->nullable();
            $table->date('ExpiredDate')->nullable();
            $table->enum('VendorStatus', ['Active', 'Inactive', 'Deactivated'])->default('Active');
            $table->datetime('LastSyncDate')->nullable();
            $table->timestamps();
            $table->foreign('UserID')->references('UserID')->on('users')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
