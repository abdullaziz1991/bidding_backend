<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id(); // id أساسي
            $table->unsignedBigInteger('user_id'); // ربط مع المستخدم
            $table->mediumText('content')->nullable();
            $table->timestamps(); // created_at & updated_at
            // المفتاح الأجنبي
            $table->foreign('user_id')
                  ->references('id') // أو 'userId' إذا غيّرت primary key بالموديل
                  ->on('users')
                  ->onDelete('cascade'); // إذا انمسح المستخدم تنمسح إشعاراته
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};


// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;


// return new class extends Migration {
//     public function up(): void
//     {
//         Schema::create('notifications', function (Blueprint $table) {
//             $table->id(); // id أساسي
//             $table->unsignedBigInteger('user_id'); // ربط مع المستخدم
//             $table->mediumText('content')->nullable();
//             $table->boolean('is_read')->default(false); // هل تم قراءة الإشعار؟
//             $table->timestamps(); // created_at & updated_at

//             // المفتاح الأجنبي
//             $table->foreign('user_id')
//                   ->references('id') // أو 'userId' إذا غيّرت primary key بالموديل
//                   ->on('users')
//                   ->onDelete('cascade'); // إذا انمسح المستخدم تنمسح إشعاراته
//         });
//     }

//     public function down(): void
//     {
//         Schema::dropIfExists('notifications');
//     }
// };