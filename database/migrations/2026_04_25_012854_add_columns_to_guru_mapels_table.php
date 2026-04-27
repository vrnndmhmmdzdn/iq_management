<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guru_mapels', function (Blueprint $table) {
            $table->foreignId('guru_id')->after('id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->after('guru_id')->constrained('mata_pelajarans')->cascadeOnDelete();
            $table->unique(['guru_id', 'mata_pelajaran_id']);
        });
    }

    public function down(): void
    {
        Schema::table('guru_mapels', function (Blueprint $table) {
            $table->dropForeign(['guru_id']);
            $table->dropForeign(['mata_pelajaran_id']);
            $table->dropColumn(['guru_id', 'mata_pelajaran_id']);
        });
    }
};