<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ustaz', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama');
            $table->string('nomor_hp', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });

        Schema::create('orang_tua', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama');
            $table->string('nomor_hp', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
        });

        Schema::create('santri', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nis', 30)->unique();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->foreignId('orang_tua_id')->nullable()->constrained('orang_tua')->nullOnDelete();
            $table->foreignId('ustaz_id')->nullable()->constrained('ustaz')->nullOnDelete();
            $table->date('tanggal_masuk')->nullable();
            $table->unsignedTinyInteger('target_hafalan')->default(30);
            $table->enum('status', ['aktif', 'lulus', 'keluar'])->default('aktif');
            $table->timestamps();
        });

        Schema::create('setoran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santri')->cascadeOnDelete();
            $table->foreignId('ustaz_id')->constrained('ustaz')->cascadeOnDelete();
            $table->enum('jenis_setoran', ['sabaq', 'sabqy', 'qiroah', 'manzil']);
            $table->date('tanggal_setoran');
            $table->unsignedTinyInteger('juz_mulai')->nullable();
            $table->unsignedTinyInteger('juz_selesai')->nullable();
            $table->string('surat_mulai', 60)->nullable();
            $table->string('surat_selesai', 60)->nullable();
            $table->unsignedSmallInteger('ayat_mulai')->nullable();
            $table->unsignedSmallInteger('ayat_selesai')->nullable();
            $table->unsignedSmallInteger('jumlah_halaman')->nullable();
            $table->unsignedTinyInteger('kelancaran')->nullable();
            $table->unsignedTinyInteger('tajwid')->nullable();
            $table->unsignedTinyInteger('makhraj')->nullable();
            $table->unsignedSmallInteger('jumlah_kesalahan')->nullable();
            $table->unsignedTinyInteger('nilai')->nullable();
            $table->string('status', 20)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->index(['santri_id', 'tanggal_setoran']);
            $table->index(['jenis_setoran', 'tanggal_setoran']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setoran');
        Schema::dropIfExists('santri');
        Schema::dropIfExists('orang_tua');
        Schema::dropIfExists('ustaz');
    }
};
