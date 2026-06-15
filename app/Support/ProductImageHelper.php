<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductImageHelper
{
    public static function storageBaseUrl(): string
    {
        $url = rtrim((string) config('app.url'), '/');

        // Dev fallback when APP_URL has no port but `php artisan serve` uses :8000
        if (preg_match('#^https?://(localhost|127\.0\.0\.1)$#i', $url)) {
            return $url . ':8000';
        }

        return $url;
    }

    public static function resolveUrls(?array $images): array
    {
        if (empty($images)) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn ($path) => self::resolveUrl(is_string($path) ? $path : null),
            $images
        )));
    }

    public static function resolveUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return self::normalizeAbsoluteUrl($path);
        }

        return self::storageBaseUrl() . '/storage/' . ltrim($path, '/');
    }

    /**
     * Fix legacy URLs generated with APP_URL=http://localhost (no port).
     */
    public static function normalizeAbsoluteUrl(string $url): string
    {
        if (preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?/storage/#i', $url)) {
            $path = parse_url($url, PHP_URL_PATH) ?? '';
            $query = parse_url($url, PHP_URL_QUERY);
            $suffix = $query ? '?' . $query : '';

            return self::storageBaseUrl() . $path . $suffix;
        }

        return $url;
    }

    /**
     * @param  array<int, string>  $existingPaths
     * @return array<int, string>
     */
    public static function mergeFromRequest(Request $request, array $existingPaths = []): array
    {
        $paths = $existingPaths;

        if ($request->has('existing_images')) {
            $raw = $request->input('existing_images');
            if (is_string($raw)) {
                $decoded = json_decode($raw, true);
                $paths = is_array($decoded) ? $decoded : $paths;
            } elseif (is_array($raw)) {
                $paths = $raw;
            }
        }

        /** @var array<int, UploadedFile> $files */
        $files = $request->file('images', []);
        if (!is_array($files)) {
            $files = $files ? [$files] : [];
        }

        foreach ($files as $file) {
            if ($file && $file->isValid()) {
                $paths[] = $file->store('products', 'public');
            }
        }

        return array_values(array_filter($paths));
    }
}
