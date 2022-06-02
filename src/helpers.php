<?php

if (! function_exists('formatBytes')) {
    /**
     * Format bytes to kb, mb, gb, tb
     *
     * @param int $size
     * @param int $precision
     * @return int|string
     */
    function formatBytes(int $size, int $precision = 2): int|string
    {
        if ($size > 0) {
            $size     = (int) $size;
            $base     = log($size) / log(1024);
            $suffixes = [' bytes', ' KB', ' MB', ' GB', ' TB'];

            return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
        } else {
            return $size;
        }
    }
}
