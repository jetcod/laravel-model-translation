<?php

namespace Jetcod\Laravel\Translation\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;
use Jetcod\Laravel\Translation\Models\Translation;

trait TranslatableTrait
{
    private $cachedTranslationModels;

    public function __get($key)
    {
        if ($this->isRelation($key)) {
            return $this->getRelationValue($key);
        }

        // Check if the attribute translation exists
        $translation = $this->getTranslation($key);
        if ($translation instanceof Translation) {
            return $translation->value;
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
     * Retrieves the list of attributes that are translatable for the current model.
     *
     * If the `TRANSLATABLE_ATTRIBUTES` constant is defined on the model, it will be returned.
     * Otherwise, `null` will be returned, indicating that all attributes are translatable.
     *
     * @return null|array the list of translatable attributes, or `null` if all attributes are translatable
     */
    protected function getTranslatableAttributes()
    {
        if (defined('static::TRANSLATABLE_ATTRIBUTES')) {
            return static::TRANSLATABLE_ATTRIBUTES;
        }

        return null;
    }

    /**
     * Retrieves the translation for the given attribute key, if the attribute is translatable.
     *
     * @param string $key the attribute key to retrieve the translation for
     *
     * @return null|Translation the translation model, or null if the attribute is not translatable
     */
    private function getTranslation(string $key): ?Translation
    {
        if ($this->isTranslatableAttribute($key)) {
            $collection = $this->getCachedTranslations();

            return $collection->firstWhere('key', $key);
        }

        return null;
    }

    /**
     * Determines whether the given attribute key is a translatable attribute.
     *
     * @param string $key the attribute key to check
     *
     * @return bool `true` if the attribute is translatable, `false` otherwise
     */
    private function isTranslatableAttribute(string $key): bool
    {
        $translatables = $this->getTranslatableAttributes();

        if (is_null($translatables)) {
            return true;
        }

        return is_array($translatables) ? in_array($key, $translatables) : $key == $translatables;
    }

    /**
     * Retrieves the cached translation models for the current locale.
     *
     * @return Collection the collection of translation models
     */
    private function getCachedTranslations(): Collection
    {
        return $this->cachedTranslationModels = $this->cachedTranslationModels ?: $this->translation()->locale(app()->getLocale())->get();
    }
}
