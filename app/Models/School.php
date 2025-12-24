<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

class School extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

    protected $fillable = [
        'image',
        'banner',
        'album',
        'intro_image',
        'download_file',
        'announce_image',
        'announce_title',
        'enrollment_quota',
        'short_name',
        'publish',
        'follow',
        'user_id',
        'statistics_majors',
        'statistics_students',
        'statistics_courses',
        'statistics_satisfaction',
        'statistics_employment',
        'is_show_statistics',
        'is_show_intro',
        'is_show_announce',
        'is_show_advantage',
        'is_show_suitable',
        'is_show_majors',
        'is_show_study_method',
        'is_show_feedback',
        'is_show_event',
        'is_show_value',
        'form_script',
        'form_tai_lo_trinh_hoc',
        'form_tu_van_mien_phi',
        'form_hoc_thu',
        'graduation_system',
        'training_majors',
        'exam_location',
    ];

    protected $table = 'schools';

    protected $casts = [
        'album' => 'array',
        'form_tai_lo_trinh_hoc' => 'array',
        'form_tu_van_mien_phi' => 'array',
        'form_hoc_thu' => 'array',
    ];

    public function languages(){
        return $this->belongsToMany(Language::class, 'school_language', 'school_id', 'language_id')
            ->using(SchoolLanguage::class)
            ->withPivot(
                'name',
                'description',
                'content',
                'canonical',
                'meta_title',
                'meta_keyword',
                'meta_description',
                'intro',
                'announce',
                'advantage',
                'suitable',
                'majors',
                'study_method',
                'feedback',
                'event',
                'value'
            )->withTimestamps();
    }

    public function majors(){
        return $this->belongsToMany(Major::class, 'school_major', 'school_id', 'major_id')
            ->withTimestamps();
    }
}
