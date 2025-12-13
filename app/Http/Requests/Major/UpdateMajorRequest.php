<?php

namespace App\Http\Requests\Major;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMajorRequest extends FormRequest
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
        $majorId = $this->route('id');
        // Lấy language_id từ locale hiện tại
        $locale = app()->getLocale();
        $language = \App\Models\Language::where('canonical', $locale)->first();
        $languageId = $language ? $language->id : null;
        
        // Lấy id của bản ghi major_language hiện tại để ignore
        $currentMajorLanguageId = null;
        if ($majorId && $languageId) {
            $currentMajorLanguage = \Illuminate\Support\Facades\DB::table('major_language')
                ->where('major_id', $majorId)
                ->where('language_id', $languageId)
                ->first();
            $currentMajorLanguageId = $currentMajorLanguage ? $currentMajorLanguage->id : null;
        }
        
        return [
            'name' => 'required|string|max:255',
            'canonical' => [
                'required',
                'string',
                'max:255',
                Rule::unique('major_language', 'canonical')->ignore($currentMajorLanguageId, 'id')
            ],
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'image' => 'nullable|string',
            'study_path_file' => 'nullable|string',
            
            // Thông tin chi tiết
            'training_system' => 'nullable|string|max:255',
            'study_method' => 'nullable|string|max:255',
            'admission_method' => 'nullable|string|max:255',
            'enrollment_quota' => 'nullable|string|max:255',
            'enrollment_period' => 'nullable|string|max:255',
            'admission_type' => 'nullable|string|max:255',
            'degree_type' => 'nullable|string|max:255',
            'training_duration' => 'nullable|string|max:255',
            'total_credits' => 'nullable|string|max:255',
            
            // Toàn Cảnh Ngành
            'overview_title' => 'nullable|string|max:255',
            'overview_content' => 'nullable|string',
            'overview_image' => 'nullable|string',
            'show_overview' => 'nullable|integer|in:1,2',
            
            // Cơ hội việc làm
            'career_description' => 'nullable|string|max:255',
            'show_career' => 'nullable|integer|in:1,2',
            
            // Chọn trường
            'choose_school_content' => 'nullable|string',
            'choose_school_image' => 'nullable|string',
            'choose_school_note' => 'nullable|string|max:255',
            'show_choose_school' => 'nullable|integer|in:1,2',
            
            // Giá trị văn bằng
            'degree_value_image' => 'nullable|string',
            'degree_value_title' => 'nullable|string|max:255',
            'degree_value_description' => 'nullable|string|max:255',
            'show_degree_value' => 'nullable|integer|in:1,2',
            
            // Cảm nhận học viên
            'student_feedback_description' => 'nullable|string',
            'show_student_feedback' => 'nullable|integer|in:1,2',
            
            // Ai phù hợp
            'show_suitable' => 'nullable|integer|in:1,2',
            
            // Ưu điểm
            'show_advantage' => 'nullable|integer|in:1,2',
            
            // Bạn sẽ học được gì
            'what_learn_description' => 'nullable|string',
            'show_what_learn' => 'nullable|integer|in:1,2',
            
            // SEO
            'meta_title' => 'nullable|string|max:255',
            'meta_keyword' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            
            // Arrays
            'features' => 'nullable|array',
            'features.*.title' => 'nullable|string|max:255',
            'features.*.image' => 'nullable|string',
            
            'admission_targets' => 'nullable|array',
            'admission_targets.*.target' => 'nullable|string|max:255',
            
            'reception_places' => 'nullable|array',
            'reception_places.*.place' => 'nullable|string|max:255',
            'reception_places.*.address' => 'nullable|string|max:255',
            
            'overview_items' => 'nullable|array',
            'overview_items.*.image' => 'nullable|string',
            'overview_items.*.title' => 'nullable|string|max:255',
            'overview_items.*.description' => 'nullable|string',
            
            'suitable_items' => 'nullable|array',
            'suitable_items.*.image' => 'nullable|string',
            'suitable_items.*.title' => 'nullable|string|max:255',
            'suitable_items.*.content' => 'nullable|string',
            'suitable_items.*.suitable_person' => 'nullable|string|max:255',
            
            'advantage_items' => 'nullable|array',
            'advantage_items.*.title' => 'nullable|string|max:255',
            'advantage_items.*.image' => 'nullable|string',
            'advantage_items.*.content' => 'nullable|string',
            
            'what_learn_categories' => 'nullable|array',
            'what_learn_categories.*.title' => 'nullable|string|max:255',
            'what_learn_categories.*.items' => 'nullable|array',
            'what_learn_categories.*.items.*.image' => 'nullable|string',
            'what_learn_categories.*.items.*.title' => 'nullable|string|max:255',
            'what_learn_categories.*.items.*.content' => 'nullable|string',
            
            'career_tags' => 'nullable|array',
            'career_tags.*.icon' => 'nullable|string',
            'career_tags.*.text' => 'nullable|string|max:255',
            'career_tags.*.color' => 'nullable|string|max:7',
            
            'career_jobs' => 'nullable|array',
            'career_jobs.*.icon' => 'nullable|string',
            'career_jobs.*.title' => 'nullable|string|max:255',
            'career_jobs.*.description' => 'nullable|string|max:255',
            'career_jobs.*.salary' => 'nullable|string|max:255',
            
            'degree_value_items' => 'nullable|array',
            'degree_value_items.*.icon' => 'nullable|string',
            'degree_value_items.*.name' => 'nullable|string|max:255',
            
            'student_feedbacks' => 'nullable|array',
            'student_feedbacks.*.avatar' => 'nullable|string',
            'student_feedbacks.*.name' => 'nullable|string|max:255',
            'student_feedbacks.*.position' => 'nullable|string|max:255',
            'student_feedbacks.*.description' => 'nullable|string',
            
            'event_post_ids' => 'nullable|array',
            'event_post_ids.*' => 'nullable|integer|exists:posts,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên chuyên ngành',
            'canonical.required' => 'Vui lòng nhập đường dẫn SEO',
            'canonical.unique' => 'Đường dẫn SEO đã tồn tại',
        ];
    }
}
