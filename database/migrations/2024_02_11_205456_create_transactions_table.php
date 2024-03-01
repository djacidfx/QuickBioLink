<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('checkout_id')->unique();
            $table->unsignedBigInteger('user_id')->index('transactions_user_id_foreign');
            $table->unsignedBigInteger('plan_id')->index('transactions_plan_id_foreign');
            $table->unsignedBigInteger('coupon_id')->nullable()->index('transactions_coupon_id_foreign');
            $table->longText('billing_address')->nullable();
            $table->longText('details_before_discount')->nullable();
            $table->longText('details_after_discount')->nullable();
            $table->double('price', 10, 2);
            $table->double('tax', 10, 2)->default(0);
            $table->double('fees', 10, 2)->default(0);
            $table->double('total', 10, 2);
            $table->unsignedBigInteger('payment_gateway_id')->nullable()->index('transactions_payment_gateway_id_foreign');
            $table->string('payment_id')->nullable();
            $table->string('payer_id')->nullable();
            $table->string('payer_email')->nullable();
            $table->tinyInteger('type')->comment('1:Subscribing 2:Renewing 3:Upgrading 4:Downgrading');
            $table->tinyInteger('status')->default(0)->comment('0:Unpaid 1:Pending 2:Paid 3:Cancelled');
            $table->boolean('is_viewed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
