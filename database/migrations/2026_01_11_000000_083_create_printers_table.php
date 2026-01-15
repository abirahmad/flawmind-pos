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
        Schema::create('printers', function (Blueprint $table) {
            $table->unsignedInteger('id')->unsigned();
            $table->unsignedInteger('business_id')->unsigned();
            $table->string('name', 191);
            $table->enum('connection_type', ['network','windows','linux']);
            $table->enum('capability_profile', ['default','simple','sp2000','tep-200m','p822d'])->default('default');
            $table->string('char_per_line', 191)->nullable()->default(null);
            $table->string('ip_address', 191)->nullable()->default(null);
            $table->string('port', 191)->nullable()->default(null);
            $table->string('path', 191)->nullable()->default(null);
            $table->unsignedInteger('created_by')->unsigned();
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('printers');
    }
};
