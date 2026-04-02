<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->string('login_alert_last_status', 20)->nullable()->after('login_alert_recipient');
            $table->text('login_alert_last_error')->nullable()->after('login_alert_last_status');
            $table->timestamp('login_alert_last_attempt_at')->nullable()->after('login_alert_last_error');
        });
    }

    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropColumn([
                'login_alert_last_status',
                'login_alert_last_error',
                'login_alert_last_attempt_at',
            ]);
        });
    }
};
