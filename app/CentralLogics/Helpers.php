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
    function handle_file_upload(string $dir, string $format, mixed $newFile = null, null|string|array $oldFile = null): string {
        // Return old file or default if no new file provided
        if (!$newFile) {
            return $oldFile ?? 'def.png';
        }
        
        // Delete old file(s) if they exist
        $publicDisk = Storage::disk('public');
        collect((array)$oldFile)
            ->filter()
            ->each(fn($file) => $publicDisk->delete($dir . $file));
        
        // Ensure directory exists
        $publicDisk->makeDirectory($dir);
        
        // Generate unique filename and store the file
        $fileName = now()->format('Y-m-d') . '-' . uniqid() . '.' . $format;
        
        // Use the file instance directly if it's an UploadedFile
        if ($newFile instanceof \Illuminate\Http\UploadedFile) {
            $newFile->storeAs(trim($dir, '/'), $fileName, 'public');
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
