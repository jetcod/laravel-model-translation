<?php

namespace Jetcod\Laravel\Translation\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Jetcod\Laravel\Translation\Models\Translation;

trait TranslatableTrait
{
    private $cachedModel;

    public function __get($key)
    {
        if ($this->isRelation($key)) {
            return $this->getRelationValue($key);
        }

        // Check if the attribute is translatable
        if ($translation = $this->getTranslation($key)) {
            return $translation;
        }

        // Fallback to the default behavior
        return parent::__get($key);
    }

    public function translation(): MorphOne
    {
        return $this->morphOne(Translation::class, 'translatable');
    }

    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    /**
     * Returns translatable attribute(s).
     *
     * @return null|array|string
     */
    protected function getTranslatableAttributes()
    {
        if (defined('static::TRANSLATABLE_ATTRIBUTES')) {
            return static::TRANSLATABLE_ATTRIBUTES;
        }

        return null;
    }

    private function getTranslation(string $key): ?string
    {
        if ($this->isTranslatableAttribute($key)) {
            $model = $this->getCachedModel();

            if ($model instanceof Model) {
                return $key == $model->key ? $model->value : null;
            }
        }

        return null;
    }

    private function isTranslatableAttribute(string $key): bool
    {
        $translatables = $this->getTranslatableAttributes();

        if (is_null($translatables)) {
            return true;
        }

        return is_array($translatables) ? in_array($key, $translatables) : $key == $translatables;
    }

    private function getCachedModel()
    {
        return $this->cachedModel = $this->cachedModel ?: $this->translation()->locale(app()->getLocale())->first();
    }
}
