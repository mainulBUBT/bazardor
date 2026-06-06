<?php

namespace App\Traits;

/**
 * Ensures translatable attributes (those in $translatedAttributes) are
 * written to BOTH the translation model AND the main table's columns.
 *
 * Without this, astrotomic/laravel-translatable only writes to the
 * translation table, leaving NOT NULL columns on the main table empty.
 *
 * Place this trait AFTER Translatable in the model's use statements.
 */
trait SyncsTranslatedAttributes
{
    public function setAttribute($key, $value)
    {
        [$attribute, $locale] = $this->getAttributeAndLocale($key);

        if ($this->isTranslationAttribute($attribute)) {
            $this->getTranslationOrNew($locale)->$attribute = $value;

            // Also write to main table for the default locale
            if ($locale === get_default_locale()) {
                parent::setAttribute($key, $value);
            }

            return $this;
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Override astrotomic's fill() to also write translatable attributes
     * to the main table for the default locale.
     *
     * Astrotomic's fill() removes translatable keys from the attributes
     * array before calling parent::fill(), which means setAttribute() is
     * never called for them. This override restores them for the default locale.
     */
    public function fill(array $attributes)
    {
        $defaultLocale = get_default_locale();
        $mainTableAttributes = [];

        foreach ($attributes as $key => $value) {
            [$attribute, $locale] = $this->getAttributeAndLocale($key);

            if ($this->isTranslationAttribute($attribute) && $locale === $defaultLocale) {
                $mainTableAttributes[$attribute] = $value;
            }
        }

        // Let astrotomic handle translations, then sync main table
        $result = parent::fill($attributes);

        foreach ($mainTableAttributes as $key => $value) {
            parent::setAttribute($key, $value);
        }

        return $result;
    }
}
