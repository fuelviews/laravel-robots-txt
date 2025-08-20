<?php

namespace Fuelviews\RobotsTxt\Commands;

use Fuelviews\RobotsTxt\RobotsTxt;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

/**
 * Class GenerateRobotsTxtCommand
 *
 * This command generates and saves the robots.txt file manually.
 * It provides options for clearing cache and specifying custom output paths.
 */
class GenerateRobotsTxtCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'robots-txt:generate 
                            {--clear-cache : Clear the robots.txt cache before generating}
                            {--disk= : Specify the disk to save to (default: config value)}
                            {--path= : Specify the path to save to (default: robots-txt/robots.txt)}
                            {--force : Force overwrite existing files}';

    /**
     * The console command description.
     */
    protected $description = 'Generate and save the robots.txt file manually';

    /**
     * Execute the console command.
     */
    public function handle(RobotsTxt $robotsTxt): int
    {
        if ($this->option('clear-cache')) {
            $this->clearCache();
        }

        $this->info('Generating robots.txt file...');

        try {
            $content = $robotsTxt->generate();

            $this->displayContent($content);

            if ($this->shouldSaveToFile()) {
                $this->saveToFile($robotsTxt, $content);
            }

            $this->info('✓ Robots.txt generated successfully!');

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to generate robots.txt: '.$e->getMessage());

            return self::FAILURE;
        }
    }

    /**
     * Clear the robots.txt cache.
     */
    protected function clearCache(): void
    {
        Cache::forget('robots-txt.checksum');
        $this->info('✓ Robots.txt cache cleared');
    }

    /**
     * Display the generated content.
     */
    protected function displayContent(string $content): void
    {
        $this->line('');
        $this->line('<fg=yellow>Generated robots.txt content:</>');
        $this->line('<fg=gray>────────────────────────────────────</>');
        $this->line($content);
        $this->line('<fg=gray>────────────────────────────────────</>');
        $this->line('');
    }

    /**
     * Determine if we should save to file.
     */
    protected function shouldSaveToFile(): bool
    {
        return $this->option('disk') || $this->option('path') || $this->option('force') ||
               $this->confirm('Save to file system?', true);
    }

    /**
     * Save the content to a file.
     */
    protected function saveToFile(RobotsTxt $robotsTxt, string $content): void
    {
        $disk = $this->option('disk') ?: config('robots-txt.disk', 'public');
        $path = $this->option('path') ?: 'robots-txt/robots.txt';

        $this->info("Saving to disk '{$disk}' at path '{$path}'...");

        try {
            $robotsTxt->saveToFile($disk, $path);
            $this->info('✓ File saved successfully');
        } catch (\Exception $e) {
            $this->error('Failed to save file: '.$e->getMessage());
        }
    }
}
