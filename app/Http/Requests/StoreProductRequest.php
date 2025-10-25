<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'sellerId' => 'required|integer',
            'sellerNumber' => 'required|string',
            'productCategory' => 'required|string',
            'productTitle' => 'required|string',
            'productDetails' => 'required|string',
            'productImage' => 'required|string',
            'productImageNumber' => 'required|string',
            'productPrice' => 'required|numeric',
            'productStatus' => 'required|string',
            'productStatusRatio' => 'required|numeric',
            'productAmount' => 'required|integer',
            'biddingDays' => 'required|integer',
            'bidDetails' => 'required|string',
            'productCurrency' => 'required|string',
            'productCountry' => 'required|string',
            'productCity' => 'required|string',
        ];
    }
}
