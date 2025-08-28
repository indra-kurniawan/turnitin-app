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
        Schema::create('pengajuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('judul_skripsi');
            $table->string('pembimbing')->nullable();
            $table->string('file_skripsi'); // path di storage
            $table->enum('status', ['pending', 'diproses', 'selesai', 'ditolak'])->default('pending');
            $table->decimal('similarity_score', 5, 2)->nullable();
            $table->string('surat_keterangan')->nullable(); // path file pdf
            $table->string('hasil_turnitin')->nullable(); // path file pdf
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('pembimbing_id')->nullable()->constrained('pembimbings')->nullOnDelete();
            $table->string('no_hp')->nullable();
            $table->string('prodi');
            $table->enum('jenis_naskah', ['proposal', 'skripsi'])->default('skripsi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuans');
    }
};
