<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $clientPublicKey = env('CLIENT_PUBLIC_KEY');

        return sodium_crypto_sign_verify_detached(
            base64_decode($this->header('X-API-Signature')),
            $this->getMethod() . ';/' . $this->path() . ';' . $this->getContent(),
            base64_decode($clientPublicKey)
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
