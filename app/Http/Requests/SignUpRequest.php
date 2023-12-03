<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class SignUpRequest extends FormRequest
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
            //
            'name'=>'required|max:255',
            'email'=>'email|required',
            'password'=>'required',
            'NumberPhone'=>'required'
        ];
    }
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required'=>'Name must not be empty',
            'name.max' =>  'Maximum 255 characters allowed',
            'email.required'=>'Email must not be empty',
            'email.email'=>'Email invalidate',
            'password.required'=>'Password must not be empty',
            'NumberPhone.required'=>'Phone number must not be empty',
        ];
    }
}
