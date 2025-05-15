<?php

return [
    /**
     * The disk where the robots.txt file will be saved
     */
    'disk' => 'public',

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
