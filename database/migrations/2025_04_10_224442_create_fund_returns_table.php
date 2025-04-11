<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fund_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fund_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->enum('frequency', ['monthly', 'quarterly', 'yearly']);
            $table->decimal('return_percentage', 5, 2);
            $table->boolean('is_compounding');
            $table->decimal('value_before', 15, 2);
            $table->decimal('value_after', 15, 2);
            $table->boolean('reverted')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['fund_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fund_returns');
    }
};
