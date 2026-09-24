<?php

namespace Fuelviews\RobotsTxt;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

/**
 * Class RobotsTxt
 *
 * This class provides functionality for generating and saving the robots.txt file.
 */
class RobotsTxt
{
    protected string $disk;

    protected string $path = 'robots-txt/robots.txt';

    protected string $cacheKey = 'robots-txt.checksum';

    public function __construct()
    {
        $this->disk = Config::get('robots-txt.disk', 'public');
    }

    /**
     * Get the robots.txt content.
     *
     * If regeneration is needed, it will automatically regenerate the file.
     */
    public function getContent(): string
    {
        if ($this->needsRegeneration()) {
            $this->regenerate();
        }

        return Storage::disk($this->disk)->get($this->path);
    }

    /**
     * Determine if regeneration is needed.
     *
     * Checks if file exists and if configuration has changed.
     */
    protected function needsRegeneration(): bool
    {
        if (! Storage::disk($this->disk)->exists($this->path)) {
            return true;
        }

        $currentChecksum = $this->computeChecksum();
        $storedChecksum = Cache::get($this->cacheKey, '');

        return $currentChecksum !== $storedChecksum;
    }

    /**
     * Regenerate and cache the robots.txt file.
     */
    protected function regenerate(): void
    {
        $content = $this->generate();

        $filesystem = Storage::disk($this->disk);
        $filesystem->put($this->path, $content);

        // Set public visibility if supported
        if (method_exists($filesystem, 'setVisibility')) {
            try {
                $filesystem->setVisibility($this->path, 'public');
            } catch (\Exception $e) {
                // Silently ignore visibility errors for non-supporting drivers
            }
        }

        Cache::forever($this->cacheKey, $this->computeChecksum());
    }

    protected function computeChecksum(): string
    {
        $appEnv = Config::get('app.env');
        $appUrl = Config::get('app.url');

        return md5(serialize([
            Config::get('robots-txt'),
            $appEnv,
            $appUrl,
        ]));
    }

    public function generate(): string
    {
        $appEnv = Config::get('app.env');
        $appUrl = rtrim(Config::get('app.url'), '/');

        if ($appEnv !== 'production') {
            return "User-agent: *\nDisallow: /";
        }

        $rules = Config::get('robots-txt.user_agents', []);

        $txt = '';

        foreach ($rules as $agent => $directives) {
            $txt .= sprintf('User-agent: %s%s', $agent, PHP_EOL);
            foreach ($directives as $directive => $paths) {
                foreach ($paths as $path) {
                    $txt .= sprintf('%s: %s%s', $directive, $path, PHP_EOL);
                }
            }

            $txt .= "\n";
        }

        $sitemaps = Config::get('robots-txt.sitemap', []);

        foreach ($sitemaps as $sitemap) {
            $txt .= 'Sitemap: '.$appUrl.'/'.($sitemap.PHP_EOL);
        }

        return $txt;
    }

    /**
     * Keep public/robots.txt in sync with the configured rules.
     *
     * Web servers commonly answer /robots.txt from the filesystem before the
     * request reaches Laravel (Laravel Forge's default site config has an exact
     * `location = /robots.txt` block with no try_files, plus
     * `error_page 404 /index.php`). With no file on disk that serves the route's
     * body with a 404 status, which crawlers treat as "no robots.txt". Writing
     * the file lets the web server answer 200 directly.
     *
     * Rewrites only when the content changed. Never throws: a read-only or
     * missing public directory falls back to the dynamic route.
     */
    public function syncStaticFile(?string $path = null): void
    {
        $path ??= public_path('robots.txt');

        try {
            $content = $this->generate();

            if (is_file($path) && file_get_contents($path) === $content) {
                return;
            }

            if (! is_dir(dirname($path)) || ! is_writable(dirname($path))) {
                return;
            }

            $temporary = $path.'.'.getmypid().'.tmp';

            if (file_put_contents($temporary, $content) === false) {
                return;
            }

            rename($temporary, $path);
        } catch (\Throwable) {
            // Fall back to the dynamic route.
        }
    }

    /**
     * Save the generated robots.txt content to a file.
     */
    public function saveToFile(string $disk, string $path): void
    {
        $content = $this->generate();
        Storage::disk($disk)->put($path, $content);
        Storage::disk($disk)->setVisibility($path, 'public');
    }
}
