<?php

namespace App\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\SchoolLanguage;
use App\Models\Language;

class UpdateSchoolRequest extends FormRequest
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
        $schoolId = $this->route('id');
        $locale = app()->getLocale();
        $language = Language::where('canonical', $locale)->first();
        $languageId = $language ? $language->id : 1;

        $currentSchoolLanguage = SchoolLanguage::where('school_id', $schoolId)
                                                ->where('language_id', $languageId)
                                                ->first();
        $currentSchoolLanguageId = $currentSchoolLanguage ? $currentSchoolLanguage->id : null;

        return [
            'name' => 'required|string|max:255',
            'canonical' => 'required|unique:routers,canonical, '.$this->id.',module_id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên trường',
            'canonical.required' => 'Vui lòng nhập đường dẫn SEO',
            'canonical.unique' => 'Đường dẫn SEO đã tồn tại',
        ];
    }
}
