<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

class Contact extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

    protected $fillable = [
        'id',
        'name',
        'phone',
        'email',
        'address',
        'product_id',
        'post_id',
        'publish',
        'created_at',
        'type',
        'message',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content'
    ];

    protected $table = 'contacts';

    public function products(){
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function posts(){
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

}