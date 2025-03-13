<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->boolean('warning_sent')->default(false);
        });
    }

    public function down()
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropColumn('warning_sent');
        });
    }
}; 