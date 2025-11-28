<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MajorCatalogueLanguage extends Model
{
    use HasFactory;

    protected $table = 'major_catalogue_language';

    public function major_catalogue(){
        return $this->belongsTo(MajorCatalogue::class, 'major_catalogue_id', 'id');
    }
}

