<?php

/**
 * Configuration File: robots-txt.php
 *
 * This file contains configuration options for the robots.txt generation.
 */

return [
    /**
     * The disk where the robots.txt file will be saved
     */
    'disk' => 'public',

    /**
     * The domain pattern to deny access in development environment
     */
    'deny_development_url' => 'development.',

    /**
     * User agent rules for different paths
     */
    'user_agents' => [
        '*' => [
            'Allow' => [
                '/'
            ],
            'Disallow' => [
                '/admin',
                '/dashboard',
            ],
        ],
    ],

    /**
     * List of sitemaps to include in robots.txt
     */
    'sitemap' => [
        'sitemap.xml',
    ],
];
