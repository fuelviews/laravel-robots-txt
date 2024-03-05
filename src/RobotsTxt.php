<?php

namespace Fuelviews\RobotsTxt;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class RobotsTxt
 *
 * This class provides functionality for generating and saving the robots.txt file.
 */
class RobotsTxt
{
    /**
     * Save the generated robots.txt content to a file.
     *
     * @param  string  $disk
     * @param  string  $path
     * @return void
     */
    public function saveToFile($disk, $path): void
    {
        $content = $this->generate();
        Storage::disk($disk)->put($path, $content);
        Storage::disk($disk)->setVisibility($path, 'public');
    }

    /**
     * Generate the content of the robots.txt file.
     *
     * @return string
     */
    public function generate(): string
    {
        $appEnv = Config::get('app.env');
        $appUrl = Config::get('app.url');
        $urlBlockPattern = Config::get('robots-txt.deny_development_url');

        if ($appEnv !== 'production' || Str::contains($appUrl, $urlBlockPattern)) {
            return "User-agent: *\nDisallow: /";
        }

        // Otherwise, generate the normal robots.txt content
        $rules = Config::get('robots-txt.user_agents', []);
        $sitemap = Config::get('robots-txt.sitemap', ['sitemap.xml']);

        $txt = "";

        foreach ($rules as $agent => $directives) {
            $txt .= "User-agent: $agent\n";
            foreach ($directives as $directive => $paths) {
                foreach ($paths as $path) {
                    $txt .= "$directive: $path\n";
                }
            }
            $txt .= "\n";
        }

        $sitemaps = Config::get('robots-txt.sitemap', []);

        foreach ($sitemaps as $sitemap) {
            $txt .= "Sitemap: " . $appUrl . "$sitemap\n";
        }

        return $txt;
    }
}
