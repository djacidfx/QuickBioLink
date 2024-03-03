<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('short_description', 150);
            $table->longText('translations')->nullable();
            $table->tinyInteger('interval')->comment('1:Monthly 2:Yearly');
            $table->double('price', 10, 2)->default(0);
            $table->text('settings');
            $table->boolean('advertisements')->default(false);
            $table->longText('custom_features')->nullable();
            $table->boolean('is_free')->default(false)->comment('0:No 1:Yes');
            $table->boolean('is_featured')->default(false)->comment('0:No 1:Yes');
            $table->integer('position')->nullable();
            $table->timestamps();
        });

        $plans = array(
            array('id' => '1', 'name' => 'Free', 'short_description' => 'Free Plan', 'translations' => NULL, 'interval' => '1', 'price' => '0.00', 'settings' => '{"biolink_limit":"5","biopage_limit":"5","hide_branding":"1"}', 'advertisements' => '1', 'custom_features' => NULL, 'is_free' => '1', 'is_featured' => '1', 'position' => NULL, 'created_at' => now(), 'updated_at' => now())
        );

        DB::table('plans')->insert($plans);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans');
    }
};
