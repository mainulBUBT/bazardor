<?php

namespace App\Traits;

trait SavesTranslations
{
    protected function saveTranslations($model, array $data, array $fields): void
    {
        $default = get_default_locale();

        // Default locale — plain field names (name, description …)
        $t = $model->translateOrNew($default);
        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $t->setAttribute($field, $data[$field] ?? null);
            }
        }
        $t->save();

        // Non-default locales — suffixed field names (name_bn, description_bn …)
        $locales = [];
        foreach ($fields as $field) {
            foreach (array_keys($data) as $key) {
                if (preg_match('/^' . preg_quote($field, '/') . '_(.+)$/', $key, $m)) {
                    $locales[$m[1]] = true;
                }
            }
        }

        foreach (array_keys($locales) as $locale) {
            if ($locale === $default) continue;

            $t = $model->translateOrNew($locale);
            $has = false;
            foreach ($fields as $field) {
                if (isset($data["{$field}_{$locale}"])) {
                    $t->setAttribute($field, $data["{$field}_{$locale}"]);
                    $has = true;
                }
            }
            $has ? $t->save() : ($t->exists && $t->delete());
        }
    }
}
