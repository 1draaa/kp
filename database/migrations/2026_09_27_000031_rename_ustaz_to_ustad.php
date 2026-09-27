<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('santri', function (Blueprint $table) {
            $table->dropForeign(['ustaz_id']);
        });
        Schema::table('setoran', function (Blueprint $table) {
            $table->dropForeign(['ustaz_id']);
        });

        Schema::rename('ustaz', 'ustad');

        Schema::table('santri', function (Blueprint $table) {
            $table->renameColumn('ustaz_id', 'ustad_id');
        });
        Schema::table('setoran', function (Blueprint $table) {
            $table->renameColumn('ustaz_id', 'ustad_id');
        });

        Schema::table('santri', function (Blueprint $table) {
            $table->foreign('ustad_id')->references('id')->on('ustad')->nullOnDelete();
        });
        Schema::table('setoran', function (Blueprint $table) {
            $table->foreign('ustad_id')->references('id')->on('ustad')->cascadeOnDelete();
        });

        DB::table('users')->where('role', 'ustaz')->update(['role' => 'ustad']);
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'ustad')->update(['role' => 'ustaz']);

        Schema::table('santri', function (Blueprint $table) {
            $table->dropForeign(['ustad_id']);
        });
        Schema::table('setoran', function (Blueprint $table) {
            $table->dropForeign(['ustad_id']);
        });

        Schema::table('santri', function (Blueprint $table) {
            $table->renameColumn('ustad_id', 'ustaz_id');
        });
        Schema::table('setoran', function (Blueprint $table) {
            $table->renameColumn('ustad_id', 'ustaz_id');
        });

        Schema::rename('ustad', 'ustaz');

        Schema::table('santri', function (Blueprint $table) {
            $table->foreign('ustaz_id')->references('id')->on('ustaz')->nullOnDelete();
        });
        Schema::table('setoran', function (Blueprint $table) {
            $table->foreign('ustaz_id')->references('id')->on('ustaz')->cascadeOnDelete();
        });
    }
};
