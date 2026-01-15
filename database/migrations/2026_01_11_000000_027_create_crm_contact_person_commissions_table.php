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
        Schema::create('crm_contact_person_commissions', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unsigned();
            $table->integer('contact_person_id');
            $table->integer('transaction_id')->nullable()->default(null);
            $table->decimal('commission_amount', 22, 4);
            $table->timestamp('created_at')->nullable()->default(null);
            $table->timestamp('updated_at')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_contact_person_commissions');
    }
};
