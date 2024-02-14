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
        Schema::create('mining_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor('\App\Models\User');
            $table->foreignIdFor('\App\Models\MiningConfig');
            $table->foreignIdFor('\App\Models\MiningStack');
            $table->decimal('earned');
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
        Schema::dropIfExists('mining_histories');
    }
};
