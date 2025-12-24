<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

class MajorCatalogue extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

    protected $fillable = [
        'image',
        'icon',
        'album',
        'publish',
        'follow',
        'order',
        'user_id',
    ];

    protected $table = 'major_catalogues';

    protected $attributes = [
        'publish' => 2,
    ];

    public function languages(){
        return $this->belongsToMany(Language::class, 'major_catalogue_language', 'major_catalogue_id', 'language_id')
            ->withPivot(
                'major_catalogue_id',
                'language_id',
                'name',
                'canonical',
                'meta_title',
                'meta_keyword',
                'meta_description',
                'description',
                'content'
            )->withTimestamps();
    }

    public function majors(){
        return $this->hasMany(Major::class, 'major_catalogue_id', 'id');
    }

    public function major_catalogue_language(){
        return $this->hasMany(MajorCatalogueLanguage::class, 'major_catalogue_id', 'id')->where('language_id', '=', 1);
    }
}

