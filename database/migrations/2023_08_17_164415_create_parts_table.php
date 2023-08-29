<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('contact_people_id');
            $table->string('part_number');
            $table->string('brand');
            $table->text('description')->nullable();
            $table->string('package')->nullable();
            $table->string('datecode')->nullable();
            $table->string('leadtime')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('twd_price', 16, 4)->nullable();
            $table->decimal('usd_price', 16, 4)->nullable();
            $table->timestamps();

            $table->unique(['supplier_id', 'part_number', 'brand']);

            $table->foreign('supplier_id')->references('id')->on('suppliers')->cascadeOnDelete();
            $table->foreign('contact_people_id')->references('id')->on('contact_people')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parts');
    }
};
