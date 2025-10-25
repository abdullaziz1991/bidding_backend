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
        Schema::create('biddings', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->primary();
            $table->foreignId('productOwnerId')->constrained('users')->onDelete('cascade');
            $table->mediumText('bidding_details');
            $table->integer('biddingDays');
            $table->dateTime('biddingEndDate');
            $table->mediumText('comments')->nullable();
            $table->tinyInteger('biddingStatus')->default(1);
            $table->integer('numberOfAlerts')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biddings');
    }
};





// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
//     /**
//      * Run the migrations.
//      */
//     public function up(): void
//     {
//         Schema::create('biddings', function (Blueprint $table) {
//             $table->id('product_id');
//             $table->unsignedBigInteger('productOwnerId');
//             $table->mediumText('bidding_details');
//             $table->integer('biddingDays');
//             $table->dateTime('biddingEndDate');
//             $table->mediumText('comments')->nullable();
//             $table->tinyInteger('biddingStatus')->default(1);
//             $table->integer('numberOfAlerts')->default(0);
//             $table->timestamps();
//         });
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('biddings');
//     }
// };
