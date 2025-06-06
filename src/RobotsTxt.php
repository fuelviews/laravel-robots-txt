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

    public function getContent(): string
    {
        if ($this->needsRegeneration()) {
            $this->regenerate();
        }

        return Storage::disk($this->disk)->get($this->path);
    }

    protected function needsRegeneration(): bool
    {
        $currentChecksum = $this->computeChecksum();
        $storedChecksum = Cache::get($this->cacheKey, '');
        if (! Storage::disk($this->disk)->exists($this->path)) {
            return true;
        }

        return $currentChecksum !== $storedChecksum;
    }

    protected function regenerate(): void
    {
        $content = $this->generate();

        Storage::disk($this->disk)->put($this->path, $content);
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
            $txt .= 'Sitemap: '.$appUrl.'/'.($sitemap . PHP_EOL);
        }

        return $txt;
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
