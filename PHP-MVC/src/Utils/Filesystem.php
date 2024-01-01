<?php

namespace App\Utils;

class Filesystem
{
    public static function getFqcns(string $baseDir, string $namespacePrefix): array
    {
        $files = array_slice(scandir($baseDir), 2);
        $classNames = array_map(fn (string $file) => pathinfo($file, PATHINFO_FILENAME), $files);
        return array_map(fn (string $className) => $namespacePrefix . $className, $classNames);
    }
}
