<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MajorLanguage extends Pivot
{
    protected $table = 'major_language';

    protected $casts = [
        'feature' => 'array',
        'target' => 'array',
        'address' => 'array',
        'overview' => 'array',
        'who' => 'array',
        'priority' => 'array',
        'learn' => 'array',
        'chance' => 'array',
        'school' => 'array',
        'value' => 'array',
        'feedback' => 'array',
        'event' => 'array',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return $this->casts;
    }

    /**
     * Override getAttribute để đảm bảo casts hoạt động
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);
        
        // Nếu là JSON field và chưa được cast, decode thủ công
        if (in_array($key, ['feature', 'target', 'address', 'overview', 'who', 'priority', 'learn', 'chance', 'school', 'value', 'feedback', 'event'])) {
            if (is_string($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $decoded;
                }
            }
        }
        
        return $value;
    }
}

