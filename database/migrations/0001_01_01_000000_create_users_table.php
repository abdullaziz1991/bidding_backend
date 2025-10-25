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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('userName');
            $table->string('userEmail')->unique();
            $table->timestamp('email_verified_at')->nullable(); // ✅ لإدارة التوثيق
            $table->string('userPassword');
            $table->enum('userGender', ['male', 'female']); // gender
            $table->string('userNumber');
            $table->string('userImage')->nullable();
            $table->double('userRating')->default(0);
            $table->string('userFcmToken')->nullable();
            $table->mediumText('userRatingList')->nullable();
            $table->mediumText('favoritesList')->nullable();
            $table->mediumText('personalList')->nullable();
            $table->rememberToken(); // ✅ لتسجيل الدخول الطويل
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};

