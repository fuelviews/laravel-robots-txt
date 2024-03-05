<?php

namespace Fuelviews\RobotsTxt\Services;

use Fuelviews\RobotsTxt\RobotsTxt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

/**
 * Class RobotsTxtService
 *
 * This class provides functionality for managing the robots.txt file,
 * including generating its content, caching it, and regenerating it when necessary.
 */
class RobotsTxtService
{
    /**
     * The disk where the robots.txt file is stored.
     */
    protected mixed $disk;

    /**
     * The path to the robots.txt file.
     */
    protected string $path;

    /**
     * The cache key for storing the checksum of the robots.txt file.
     */
    protected string $cacheKey = 'robots-txt.checksum';

    /**
     * Create a new RobotsTxtService instance.
     *
     * Initializes the disk and path for the robots.txt file.
     */
    public function __construct()
    {
        $this->disk = Config::get('robots-txt.disk', 'public');
        $this->path = 'robots-txt/robots.txt';
    }

    /**
     * Get the contents of the robots.txt file.
     *
     * If the file needs to be regenerated, it will be done before retrieving its contents.
     */
    public function getContent(): string
    {
        if ($this->needsRegeneration()) {
            $this->regenerate();
        }

        return Storage::disk($this->disk)->get($this->path);
    }

    /**
     * Check if the robots.txt file needs to be regenerated.
     */
    protected function needsRegeneration(): bool
    {
        $currentChecksum = $this->computeChecksum();
        $storedChecksum = Cache::get($this->cacheKey, '');

        if (! Storage::disk($this->disk)->exists($this->path) || $currentChecksum !== $storedChecksum) {
            return true;
        }

        return false;
    }

    /**
     * Regenerate the robots.txt file and update the cache.
     *
     * The new content is generated using the RobotsTxt class.
     */
    protected function regenerate(): void
    {
        $robotTxtGenerator = new RobotsTxt();
        $content = $robotTxtGenerator->generate();

        Storage::disk($this->disk)->put($this->path, $content);
        Cache::forever($this->cacheKey, $this->computeChecksum());
    }

    /**
     * Compute the checksum for the robots.txt file.
     *
     * The checksum is based on the configuration and environment settings.
     */
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

    /**
     * Delete the robots.txt file from the public directory if it exists.
     *
     * This method is used when the robots.txt file is managed outside the application's storage.
     */
    public function deletePublicRobotsTxt(): void
    {
        $path = public_path('robots.txt');

        if (file_exists($path)) {
            @unlink($path);
        }
    }
}
