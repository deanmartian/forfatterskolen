<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('course_id')->unsigned()->index('course_id');
            $table->text('variation');
            $table->text('description');
            $table->boolean('has_coaching')->default(0);
            $table->integer('manuscripts_count')->default(0);
            $table->decimal('full_payment_price', 11);
            $table->decimal('months_3_price', 11);
            $table->decimal('months_6_price', 11);
            $table->decimal('months_12_price', 11);
            $table->string('full_price_product')->default('');
            $table->string('months_3_product')->default('');
            $table->string('months_6_product')->default('');
            $table->string('months_12_product')->default('');
            $table->integer('full_price_due_date');
            $table->integer('months_3_due_date');
            $table->integer('months_6_due_date');
            $table->integer('months_12_due_date');
            $table->integer('workshops')->nullable()->default(0);
            $table->integer('full_payment_sale_price')->nullable();
            $table->date('full_payment_sale_price_from')->nullable();
            $table->date('full_payment_sale_price_to')->nullable();
            $table->integer('months_3_sale_price')->nullable();
            $table->date('months_3_sale_price_from')->nullable();
            $table->date('months_3_sale_price_to')->nullable();
            $table->integer('months_6_sale_price')->nullable();
            $table->date('months_6_sale_price_from')->nullable();
            $table->date('months_6_sale_price_to')->nullable();
            $table->integer('months_12_sale_price')->nullable();
            $table->date('months_12_sale_price_from')->nullable();
            $table->date('months_12_sale_price_to')->nullable();
            $table->boolean('months_3_enable')->default(1);
            $table->boolean('months_6_enable')->default(1);
            $table->boolean('months_12_enable')->default(0);
            $table->decimal('full_payment_upgrade_price', 11)->default(0.00);
            $table->decimal('months_3_upgrade_price', 11)->default(0.00);
            $table->decimal('months_6_upgrade_price', 11)->default(0.00);
            $table->decimal('months_12_upgrade_price', 11)->default(0.00);
            $table->decimal('full_payment_standard_upgrade_price', 11)->default(0.00);
            $table->decimal('months_3_standard_upgrade_price', 11)->default(0.00);
            $table->decimal('months_6_standard_upgrade_price', 11)->default(0.00);
            $table->decimal('months_12_standard_upgrade_price', 11)->default(0.00);
            $table->boolean('course_type');
            $table->date('disable_upgrade_price_date')->nullable();
            $table->boolean('disable_upgrade_price')->nullable();
            $table->boolean('has_student_discount')->default(1);
            $table->boolean('is_reward')->default(0);
            $table->date('issue_date')->nullable();
            $table->boolean('validity_period')->default(0);
            $table->boolean('is_show')->default(1);
            $table->boolean('is_upgradeable')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('packages');
    }
};
