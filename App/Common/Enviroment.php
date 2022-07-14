<?php

namespace App\Common;

class Environment
{
    public static function load(string $directory)
    {
        if (!file_exists("{$directory}/.env")) {
            return false;
        }

        $lines = file("{$directory}/.env");

        foreach ($lines as $line) {
            putenv(trim($line));
        }
    }
}
