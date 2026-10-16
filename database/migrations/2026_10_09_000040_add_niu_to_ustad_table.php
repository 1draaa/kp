<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ustad', function (Blueprint $table) {
            $table->string('niu', 30)->unique()->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('ustad', function (Blueprint $table) {
            $table->dropColumn('niu');
        });
    }
};
