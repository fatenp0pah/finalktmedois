<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {

        Schema::create('proof_of_deliveries', function (Blueprint $table) {
            $table->id('ProofID');
            $table->unsignedBigInteger('DOID');
            $table->string('FileName', 100)->nullable();
            $table->string('FileType', 10)->nullable();
            $table->string('FileLink', 255);
            $table->datetime('UploadedDate')->useCurrent();
            $table->foreign('DOID')->references('DOID')->on('delivery_orders')->onDelete('cascade');
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id('LogID');
            $table->unsignedBigInteger('UserID')->nullable();
            $table->string('Action', 100);
            $table->string('AffectedRecord', 100)->nullable();
            $table->string('LogDescription', 255)->nullable();
            $table->datetime('Timestamp')->useCurrent();
        });

        Schema::create('vendor_api_logs', function (Blueprint $table) {
            $table->id('APILogID');
            $table->unsignedBigInteger('VendorID');
            $table->string('APIAction', 100);
            $table->string('APIStatus', 20)->default('Success');
            $table->datetime('LogDate')->useCurrent();
            $table->foreign('VendorID')->references('VendorID')->on('vendors')->onDelete('cascade');
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id('NotificationID');
            $table->unsignedBigInteger('UserID');
            $table->string('NotificationMessage', 255);
            $table->enum('NotificationStatus', ['Unread', 'Read'])->default('Unread');
            $table->datetime('CreatedDate')->useCurrent();
            $table->foreign('UserID')->references('UserID')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('vendor_api_logs');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('proof_of_deliveries');
    }
};
