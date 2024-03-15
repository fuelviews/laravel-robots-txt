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
