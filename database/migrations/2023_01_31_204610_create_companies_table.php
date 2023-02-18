<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('businessName', 256);
            $table->string('address', 256)->nullable();
            $table->string('vat');
            $table->string('taxCode', 256);
            $table->integer('employees')->nullable();
            $table->boolean('active')->nullable()->default(false);
            $table->enum('type', [1, 2, 3, 4]); //corrispondenza 1=>'SRL', 2=>'SPA', 3=>'SNC', 4=>'Freelance'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
};