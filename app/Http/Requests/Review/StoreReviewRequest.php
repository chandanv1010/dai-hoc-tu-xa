<?php

namespace App\Http\Requests\Review;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'fullname' => 'required|string',
            'reviewable_id' => 'gt:0',
            'score' => 'gt:0',
            'gender' => 'required|not_in:0|in:Nam,Nữ',
            // 'image' => 'required|max:5120'
        ];
    }

    public function messages(): array
    {
        return [
            'fullname.required' => 'Bạn chưa nhập tên người đánh giá',
            'fullname.string' => 'Tên người đánh giá phải là dạng ký tự',
        ];
    }
}
