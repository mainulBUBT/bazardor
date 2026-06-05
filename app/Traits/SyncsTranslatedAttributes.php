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
}
