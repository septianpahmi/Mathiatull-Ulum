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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->string('no_pembayaran')->unique();
            $table->string('nominal');
            $table->date('tgl_tagihan');
            $table->date('tgl_pembayaran')->nullable();
            $table->enum('status', ['Berhasil', 'Menunggu Pembayaran'])->default('Menunggu Pembayaran');
            $table->unsignedBigInteger('id_siswa');
            $table->timestamps();

            $table->foreign('id_siswa')->references('id')->on('siswas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pembayarans');
    }
};
