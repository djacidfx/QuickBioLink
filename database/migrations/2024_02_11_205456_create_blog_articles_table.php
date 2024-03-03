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
        Schema::create('blog_articles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('lang', 3)->index('lang');
            $table->unsignedBigInteger('user_id')->index('user_id');
            $table->unsignedBigInteger('category_id')->index('category_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('image');
            $table->text('content');
            $table->string('short_description', 200);
            $table->text('tags')->nullable();
            $table->bigInteger('views')->default(0);
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
        Schema::dropIfExists('blog_articles');
    }
};
