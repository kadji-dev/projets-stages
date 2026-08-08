<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pre_enrollments', function (Blueprint $table) {
            $table->string('telephone')->nullable()->after('nationalite');
            $table->string('email')->nullable()->after('telephone');

            if (Schema::hasColumn('pre_enrollments', 'signature')) {
                $table->dropColumn('signature');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pre_enrollments', function (Blueprint $table) {
            $table->dropColumn(['telephone', 'email']);
            $table->string('signature')->nullable();
        });
    }
};
