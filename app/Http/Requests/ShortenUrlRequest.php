<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// use Illuminate\Http\Request;

class ShortenUrlRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'url' => 'required|url',
        ];
    }
}