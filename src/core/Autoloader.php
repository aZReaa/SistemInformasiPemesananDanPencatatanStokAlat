<?php

class Autoloader
{
    public static function register()
    {
        spl_autoload_register([self::class, 'load']);
    }

    public static function load($className)
    {
        $directories = [
            __DIR__ . '/../models/',
            __DIR__ . '/../controllers/',
            __DIR__ . '/../core/',
        ];

        foreach ($directories as $directory) {
            $file = $directory . $className . '.php';
            if (file_exists($file)) {
                require_once $file;
                break;
            }
        }
    }
}