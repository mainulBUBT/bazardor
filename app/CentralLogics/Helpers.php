<?php

use App\Models\Setting;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

if (!function_exists('translate')) {
    function translate($key, $replace = [])
    {
        try {
            if(trans()->has($key)) return trans($key, $replace);
            [$file, $lang_key] = explode('.',$key,2);
            if(!isset($lang_key)){
                $file="lang";
                $lang_key = $key;
            }
            if(!$lang = App::currentLocale()){
                App::setLocale('en');
                $lang = 'en';
            }
            if(!file_exists(base_path("resources/lang/{$lang}/{$file}.php"))) $file="lang";
            if(trans()->has("{$file}.{$lang_key}")) return trans("{$file}.{$lang_key}", $replace);
            $lang_array = include(base_path("resources/lang/{$lang}/{$file}.php"));
            $processed_key = ucfirst(str_replace('_', ' ', str_ireplace(['\'', '"', ',', ';', '<', '>', '?'], ' ', $lang_key)));
            if (!array_key_exists($key, $lang_array)) {
                $lang_array[$lang_key] = $processed_key;
                $str = "<?php return " . var_export($lang_array, true) . ";";
                file_put_contents(base_path("resources/lang/{$lang}/{$file}.php"), $str);
                $result = $processed_key;
            } else {
                $result = trans("{$file}.{$lang_key}");
            }
            return $result;
        } catch (\Exception $exception) {
           info($exception->getMessage());
        }
    }
}

if (!function_exists('handle_file_upload')) {
    /**
     * Upload or update a file, deleting the old one if it exists.
     *
     * @param string $dir
     * @param string $format
     * @param mixed $newFile
     * @param null|string|array $oldFile
     * @return string
     */
    function handle_file_upload(string $dir, string $format, mixed $newFile = null, null|string|array $oldFile = null): string {
        $publicDisk = Storage::disk('public');
        $dir = trim($dir, '/') . '/';

        // Delete old file(s) if they exist
        if ($oldFile) {
            if (is_string($oldFile) && $publicDisk->exists($dir . $oldFile)) {
                $publicDisk->delete($dir . $oldFile);
            } elseif (is_array($oldFile)) {
                collect($oldFile)
                    ->filter()
                    ->each(fn($file) => $publicDisk->exists($dir . $file) ? $publicDisk->delete($dir . $file) : null);
            }
        }

        // If no new file, return default or old file name
        if (!$newFile) {
            return $oldFile && is_string($oldFile) ? $oldFile : 'def.png';
        }

        // Ensure directory exists
        $publicDisk->makeDirectory($dir);
        
        // Generate unique filename
        $fileName = now()->format('Y-m-d') . '-' . uniqid() . '.' . $format;
        
        // Store the file
        if ($newFile instanceof \Illuminate\Http\UploadedFile) {
            $newFile->storeAs($dir, $fileName, 'public');
        } else {
            $publicDisk->put($dir . $fileName, file_get_contents($newFile));
        }
        
        return $fileName;
    }
}


if (!function_exists('pagination_limit')) {
    function pagination_limit()
    {
        try {
            if (!session()->has('pagination_limit')) {
                $limit = Setting::where('key_name', 'pagination_limit')->where('settings_type', 'business_information')->first()?->value ?? DEFAULT_PAGINATION;
                session()->put('pagination_limit', $limit);
            } else {
                $limit = session('pagination_limit');
            }
        } catch (Exception $exception) {
            return DEFAULT_PAGINATION;
        }

        return $limit;
    }
}

if (!function_exists('formated_response')) {
    function formated_response($constant, $content = null, $limit =null, $offset = null ,$errors = []): array
    {
        $constant = (array)$constant;
        $constant['total_size'] = isset($limit)?$content->total():null;
        $constant['limit'] = $limit;
        $constant['offset'] = $offset;
        $constant['data'] = $content;
        $constant['errors'] = $errors;
        return $constant;
    }
}
