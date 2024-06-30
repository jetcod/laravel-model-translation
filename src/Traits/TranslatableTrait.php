<?php

namespace Jetcod\Laravel\Translation\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Jetcod\Laravel\Translation\Models\Translation;

trait TranslatableTrait
{
    private $cachedModel;

    public function translation(): MorphOne
    {
        return $this->morphOne(Translation::class, 'translatable');
    }

    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

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

    private function getTranslation($key)
    {
        $model = $this->getCachedModel();

        if ($model instanceof Model) {
            return $key == $model->key ? $model->value : null;
        }

        return null;
    }

    private function getCachedModel()
    {
        return $this->cachedModel = $this->cachedModel ?: $this->translation()->locale(app()->getLocale())->first();
    }
}
