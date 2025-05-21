<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

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