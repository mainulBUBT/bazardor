<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix JSON data left behind by the old Spatie translatable migration.
     *
     * The old migration converted plain text to JSON like {"en":"Nazirshail Rice"}.
     * Our translation table migrations copied that JSON directly.
     * This migration extracts plain text for the correct locale in both places.
     */
    public function up(): void
    {
        $defaultLocale = 'en';

        // Fix translation tables: extract plain text from JSON values
        $this->fixTranslationTable('product_translations', ['name', 'description', 'brand'], $defaultLocale);
        $this->fixTranslationTable('market_translations', ['name', 'description', 'address'], $defaultLocale);
        $this->fixTranslationTable('category_translations', ['name', 'description'], $defaultLocale);
        $this->fixTranslationTable('banner_translations', ['title'], $defaultLocale);
        $this->fixTranslationTable('zone_translations', ['name', 'description'], $defaultLocale);
        $this->fixTranslationTable('unit_translations', ['name', 'symbol'], $defaultLocale);
        $this->fixTranslationTable('product_tag_translations', ['tag'], $defaultLocale);

        // Fix main tables: extract plain text from JSON values
        $this->fixMainTable('products', ['name', 'description', 'brand'], $defaultLocale);
        $this->fixMainTable('markets', ['name', 'description', 'address'], $defaultLocale);
        $this->fixMainTable('categories', ['name', 'description'], $defaultLocale);
        $this->fixMainTable('banners', ['title'], $defaultLocale);
        $this->fixMainTable('zones', ['name'], $defaultLocale);
        $this->fixMainTable('units', ['name', 'symbol'], $defaultLocale);
        $this->fixMainTable('product_tags', ['tag'], $defaultLocale);
    }

    private function fixTranslationTable(string $table, array $fields, string $defaultLocale): void
    {
        foreach ($fields as $field) {
            $rows = DB::table($table)->select(['id', 'locale', $field])->get();

            foreach ($rows as $row) {
                $value = $row->$field;
                if ($value === null) {
                    continue;
                }

                $plain = $this->extractPlainText($value, $row->locale, $defaultLocale);

                if ($plain !== null && $plain !== $value) {
                    DB::table($table)->where('id', $row->id)->update([$field => $plain]);
                }
            }
        }
    }

    private function fixMainTable(string $table, array $fields, string $defaultLocale): void
    {
        foreach ($fields as $field) {
            if (!DB::getSchemaBuilder()->hasColumn($table, $field)) {
                continue;
            }

            $rows = DB::table($table)->select(['id', $field])->get();

            foreach ($rows as $row) {
                $value = $row->$field;
                if ($value === null) {
                    continue;
                }

                $plain = $this->extractPlainText($value, $defaultLocale, $defaultLocale);

                if ($plain !== null && $plain !== $value) {
                    DB::table($table)->where('id', $row->id)->update([$field => $plain]);
                }
            }
        }
    }

    private function extractPlainText(string $value, string $locale, string $defaultLocale): ?string
    {
        $decoded = json_decode($value, true);

        if (!is_array($decoded)) {
            return null; // Already plain text
        }

        // Try the requested locale first, then fallback, then first available
        return $decoded[$locale] ?? $decoded[$defaultLocale] ?? reset($decoded);
    }

    public function down(): void
    {
        // Reverse is not practical — the JSON data is gone after up()
    }
};
