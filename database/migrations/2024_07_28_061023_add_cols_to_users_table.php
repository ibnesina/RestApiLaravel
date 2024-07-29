<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('contract')->nullable()->after('password'); // Place this after the 'id' column or any other column before 'created_at'
            $table->string('pincode', 6)->nullable()->after('contract');
            $table->text('address')->nullable()->after('pincode');
            $table->boolean('status')->comment("1: Active, 0: Inactive")->default(1)->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            
            // $table->dropColumn('contract');
            // $table->dropColumn('pincode');
            // $table->dropColumn('address');
            // $table->dropColumn('status');
        });
    }
};
