<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64)->unique();
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 10, 2); // percentage: 0-100, fixed: amount off
            $table->decimal('min_order_amount', 10, 2)->nullable();
            $table->unsignedInteger('max_uses')->nullable(); // null = unlimited
            $table->unsignedInteger('times_used')->default(0);
            $table->unsignedTinyInteger('uses_per_user')->nullable(); // null = unlimited per user, 1 = once per user
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('active')->default(true);
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
