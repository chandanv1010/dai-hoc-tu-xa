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
        'enrollment_quota',
        'short_name',
        'publish',
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
    ];

    protected $table = 'schools';

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
