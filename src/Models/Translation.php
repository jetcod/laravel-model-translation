<?php

namespace Jetcod\Laravel\Translation\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Translation.
 *
 * @property string translatable_type
 * @property int translatable_id
 * @property string lang
 * @property string title
 * @property string text
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property Carbon deleted_at
 */
class Translation extends Model
{
    /**
     * @var string[]
     */
    protected $guarded = ['id'];

    protected $fillable = [
        'translatable_id',
        'translatable_type',
        'locale',
        'key',
        'value',
    ];

    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeLocale($query, $locale)
    {
        return $query->where('locale', $locale);
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return sprintf(
            '%s%s',
            config('translations.database.prefix'),
            config('translations.database.table_name')
        );
    }
}
