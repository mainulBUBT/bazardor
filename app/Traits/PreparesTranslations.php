<?php

namespace App\Traits;

trait PreparesTranslations
{
    /**
     * Convert form input data into astrotomic/laravel-translatable fill format.
     *
     * Input format (from admin forms):
     *   ['name' => 'English Name', 'name_bn' => 'বাংলা নাম', 'description' => 'English Desc', 'description_bn' => 'বাংলা বিবরণ']
     *
     * Output format (for astrotomic's fill()):
     *   [
     *     'en' => ['name' => 'English Name', 'description' => 'English Desc'],
     *     'bn' => ['name' => 'বাংলা নাম', 'description' => 'বাংলা বিবরণ'],
     *   ]
     */
    protected function prepareTranslations(array $data, array $translatableFields): array
    {
        $locales = get_enabled_locales();
        $defaultLocale = get_default_locale();

        // Extract locale-prefixed fields (e.g., name_bn, description_bn)
        $translations = [];

        foreach ($translatableFields as $field) {
            // Default locale value comes from the base field
            $translations[$defaultLocale][$field] = $data[$field] ?? null;

            // Non-default locales come from {field}_{locale} inputs
            foreach ($locales as $locale) {
                if ($locale === $defaultLocale) {
                    continue;
                }

                $key = "{$field}_{$locale}";

                if (isset($data[$key])) {
                    $translations[$locale][$field] = $data[$key];
                    unset($data[$key]);
                }
            }
        }

        // Merge translations back into data with locale keys
        foreach ($translations as $locale => $fields) {
            $data[$locale] = array_filter($fields, fn ($value) => $value !== null);
        }

        return $data;
    }
}
