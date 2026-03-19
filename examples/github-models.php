<?php

/**
 * Example: Generate a product description using GitHub Models (LFM-2).
 *
 * Requirements:
 *   - A GitHub personal access token (PAT) with the `models` scope.
 *     Create one at https://github.com/settings/tokens
 *   - Set it as the GITHUB_TOKEN environment variable before running:
 *       export GITHUB_TOKEN=your_token_here
 *       php examples/github-models.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Tandrezone\Description\ProductDescriptionGenerator;

$token = getenv('GITHUB_TOKEN');
if (!$token) {
    fwrite(STDERR, "Error: GITHUB_TOKEN environment variable is not set.\n");
    exit(1);
}

$generator = new ProductDescriptionGenerator($token);

$technicalInfo = [
    'brand'             => 'TechBrand',
    'model'             => 'ProBook X1',
    'cpu'               => 'Intel Core i7-1355U',
    'ram'               => '16GB DDR5',
    'storage'           => '512GB NVMe SSD',
    'display'           => '14-inch FHD IPS, 400 nits',
    'battery_life'      => 'Up to 12 hours',
    'weight'            => '1.35 kg',
    'os'                => 'Windows 11 Pro',
    'available_colors'  => ['Midnight Black', 'Arctic Silver'],
];

echo "Generating product description via GitHub Models (LFM-2)...\n\n";

$description = $generator->generate($technicalInfo);

echo $description . "\n";
