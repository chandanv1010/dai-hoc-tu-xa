<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SchoolLanguage extends Pivot
{
    protected $table = 'school_language';

    protected $casts = [
        'intro' => 'array',
        'announce' => 'array',
        'advantage' => 'array',
        'suitable' => 'array',
        'majors' => 'array',
        'study_method' => 'array',
        'feedback' => 'array',
        'event' => 'array',
        'value' => 'array',
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
        if (in_array($key, ['intro', 'announce', 'advantage', 'suitable', 'majors', 'study_method', 'feedback', 'event', 'value'])) {
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
