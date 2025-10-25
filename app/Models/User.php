<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;


class User extends Authenticatable
// class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    // protected $primaryKey = 'userId'; // بدل id
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'userName',
        'userEmail',
        'userPassword',
        'userGender',
        'userNumber',
        'userFcmToken',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'userPassword',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // //////////////////////////////////////////////////////
            // 'email_verified_at' => 'datetime',
            // 'password' => 'hashed',
        ];
    }
    // هذه تعيد البريد المخصص
    public function getEmailForPasswordReset()
    {
        return $this->userEmail;
    }

    // هذه الدالة تحدد اسم الحقل المستخدم للمصادقة
    public function getAuthIdentifierName()
    {
        return 'userEmail';
    }
    
//     public function getIdAttribute()
// {
//     return $this->userId;
// }


    // هذه الدالة تحدد حقل كلمة المرور
    public function getAuthPassword()
    {
        return $this->userPassword;
    }
    
   
}
