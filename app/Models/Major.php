<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

class Major extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

    protected $fillable = [
        'subtitle',
        'banner',
        'career_image',
        'image',
        'publish',
        'follow',
        'is_home',
        'user_id',
        'major_catalogue_id',
        'study_path_file',
        'is_show_feature',
        'is_show_overview',
        'is_show_who',
        'is_show_priority',
        'is_show_learn',
        'is_show_chance',
        'is_show_school',
        'is_show_value',
        'is_show_feedback',
        'is_show_event',
        'admission_subject',
        'exam_location',
        'form_tai_lo_trinh_json',
        'form_tu_van_mien_phi_json',
        'form_hoc_thu_json',
    ];

    protected $table = 'majors';

    protected $casts = [
        'form_tai_lo_trinh_json' => 'array',
        'form_tu_van_mien_phi_json' => 'array',
        'form_hoc_thu_json' => 'array',
    ];

    public function languages(){
        return $this->belongsToMany(Language::class, 'major_language', 'major_id', 'language_id')
            ->using(MajorLanguage::class)
            ->withPivot(
                'name',
                'description',
                'content',
                'training_system',
                'study_method',
                'admission_method',
                'enrollment_quota',
                'enrollment_period',
                'admission_type',
                'degree_type',
                'training_duration',
                'total_credits',
                'canonical',
                'meta_title',
                'meta_keyword',
                'meta_description',
                'feature',
                'target',
                'address',
                'overview',
                'who',
                'priority',
                'learn',
                'chance',
                'school',
                'value',
                'feedback',
                'event'
            )->withTimestamps();
    }

    public function schools(){
        return $this->belongsToMany(School::class, 'school_major', 'major_id', 'school_id')
            ->withTimestamps();
    }

    public function major_catalogue(){
        return $this->belongsTo(MajorCatalogue::class, 'major_catalogue_id', 'id');
    }

}
