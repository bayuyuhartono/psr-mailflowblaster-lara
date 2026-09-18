<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->foreignId('created_by')->nullable()->after('failed_count')->constrained('users')->nullOnDelete();
        });

        DB::table('email_campaigns')->orderBy('id')->eachById(function ($campaign): void {
            DB::table('email_campaigns')->where('id', $campaign->id)->update([
                'name' => 'Campaign '.$campaign->id.' - '.mb_substr($campaign->subject, 0, 220),
            ]);
        });

        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->unique('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropUnique(['name']);
            $table->dropColumn(['name', 'created_by']);
        });
    }
};
