<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::table('schedule_runs', function (Blueprint $table) {
      $table->date('base_date')->nullable()->after('run_date');
    });
  }

  public function down(): void
  {
    Schema::table('schedule_runs', function (Blueprint $table) {
      $table->dropColumn('base_date');
    });
  }
};
