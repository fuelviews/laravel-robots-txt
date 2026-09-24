<?php

return [
    /**
     * The disk where the robots.txt file will be saved
     */
    'disk' => 'public',

    /**
     * Write the generated rules to public/robots.txt after boot, so web
     * servers that serve /robots.txt from disk (e.g. Laravel Forge's default
     * site config) answer 200 instead of 404. The file is rewritten when the
     * rules, APP_ENV or APP_URL change.
     */
    'static_file' => true,

    /**w
     * User agent rules for different paths
     */
    'user_agents' => [
        '*' => [
            'Allow' => [
                '/',
            ],
            'Disallow' => [
                '/admin',
                '/dashboard',
            ],
        ],
    ],

    /**
     * Sitemaps to include in robots.txt
     */
    'sitemap' => [
        'sitemap.xml',
    ],
];
