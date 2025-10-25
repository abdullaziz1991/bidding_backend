<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'userName' => 'required|string|max:255',
            'userEmail' => 'required|email|unique:users,userEmail',
            'userPassword' => 'required|string|min:8',
            "userGender" => 'required|string',
            'userNumber' => 'required|string|min:8',
            'appVersion' => 'required|string',
        ];
    }
}

     // 'userImage' => 'nullable|string',
            // 'userRating' => 'nullable|string',
            // 'userFcmToken' => 'nullable|string',
            // 'userRatingList' => 'nullable|array',
            // 'favoritesList' => 'nullable|array',
            // 'personalList' => 'nullable|array',