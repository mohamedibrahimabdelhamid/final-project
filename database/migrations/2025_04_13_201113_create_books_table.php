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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author');
            $table->string('image')->nullable();
            $table->string('genre');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->enum('availability', ['Free', 'Purchase', 'Rent'])->default('Purchase');
            $table->date('published_date');
            $table->string('file_url'); // path to eBook file
            $table->string('text_sample')->nullable();
            $table->string('audio_sample')->nullable();
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
        Schema::dropIfExists('books');
    }
};
