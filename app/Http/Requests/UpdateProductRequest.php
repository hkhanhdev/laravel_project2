<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Response;

class UpdateProductRequest extends FormRequest
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
            'name'=>['required'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $exception = $validator->getException();
        $responseError = new Response([
            "errors"=> $validator->errors(),
            "status"=>Response::HTTP_UNPROCESSABLE_ENTITY
        ],Response::HTTP_UNPROCESSABLE_ENTITY);
        throw (new $exception($validator,$responseError));
    }
}
