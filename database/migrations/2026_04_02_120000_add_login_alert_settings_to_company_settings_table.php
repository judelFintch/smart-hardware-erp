<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->boolean('login_alert_enabled')->default(false)->after('email');
            $table->string('login_alert_recipient')->nullable()->after('login_alert_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropColumn(['login_alert_enabled', 'login_alert_recipient']);
        });
    }
};
