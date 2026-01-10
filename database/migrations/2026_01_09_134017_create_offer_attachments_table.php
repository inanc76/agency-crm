<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('offer_attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('offer_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->string('file_path'); // Minio path
            $table->string('file_name');
            $table->string('file_type'); // PDF, DOCX, etc.
            $table->bigInteger('file_size'); // bytes
            $table->timestamps();

            $table->foreign('offer_id')
                ->references('id')
                ->on('offers')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_attachments');
    }
};
