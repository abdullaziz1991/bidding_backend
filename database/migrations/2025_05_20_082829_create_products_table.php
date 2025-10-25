<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
     
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('productId'); // Primary Key
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->unsignedInteger('productCategory')->index();
            $table->string('productTitle', 255);
            $table->mediumText('productDetails');
            $table->string('productImage', 255);
            $table->double('productPrice');
            $table->unsignedInteger('productAdvertisement')->default(1)->index();
            $table->unsignedInteger('productCountry')->default(1);
            $table->unsignedInteger('productCity')->default(1);
            $table->unsignedInteger('productLikes')->default(0);
            $table->unsignedInteger('productStatus')->index();
            $table->unsignedInteger('productStatusRatio')->default(1);
            $table->unsignedInteger('productAmount')->default(1);
            $table->unsignedInteger('productCurrency')->default(1);
            $table->boolean('isApproved')->default(false);
            $table->timestamps(); // created_at and updated_at
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};




// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;
// use Illuminate\Support\Facades\DB;

// return new class extends Migration
// {
//     /**
//      * Run the migrations.
//      */
//     public function up()
//     {
//         Schema::create('products', function (Blueprint $table) {
//             $table->id('productId'); // Primary Key
//             $table->unsignedBigInteger('sellerId');
//             $table->unsignedBigInteger('sellerNumber');
//             $table->unsignedInteger('productCategory')->index();
//             $table->string('productTitle', 255);
//             $table->mediumText('productDetails');
//             $table->string('productImage', 255);
//             // $table->unsignedInteger('productImageNumber');
//             $table->double('productPrice');
//             $table->unsignedInteger('productAdvertisement')->default(1)->index();
//             $table->dateTime('productDateTime')->default(DB::raw('CURRENT_TIMESTAMP'));
//             $table->integer('biddingDays')->default(1);
//             $table->unsignedInteger('productCountry')->default(1);
//             $table->unsignedInteger('productCity')->default(1);
//             $table->unsignedInteger('productLikes')->default(0);
//             $table->unsignedInteger('productFollowers')->default(0);
//             $table->unsignedInteger('productStatus')->index();
//             $table->unsignedInteger('productStatusRatio')->default(1);
//             $table->unsignedInteger('productAmount')->default(1);
//             $table->unsignedInteger('productCurrency')->default(1);
//             $table->boolean('isApproved')->default(false);

//             $table->timestamps(); // created_at and updated_at
//         });
//     }


//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('products');
//     }
// };
