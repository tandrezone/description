<?php

/**
 * Example: Generate a product description using OpenRouter.ai.
 *
 * OpenRouter provides access to hundreds of LLMs through a single
 * OpenAI-compatible API, including free-tier models.
 *
 * Requirements:
 *   - An OpenRouter API key. Sign up at https://openrouter.ai and
 *     generate a key at https://openrouter.ai/keys
 *   - Set it as the OPENROUTER_API_KEY environment variable:
 *       export OPENROUTER_API_KEY=sk-or-...
 *       php examples/openrouter.php
 *
 * Optionally pass a model ID as the first argument to override the default
 * (liquid/lfm-2). Browse models at https://openrouter.ai/models
 *
 *       php examples/openrouter.php mistralai/mistral-7b-instruct
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Tandrezone\Description\ProductDescriptionGenerator;

$apiKey = getenv('OPENROUTER_API_KEY');
if (!$apiKey) {
    fwrite(STDERR, "Error: OPENROUTER_API_KEY environment variable is not set.\n");
    exit(1);
}

$model = $argv[1] ?? null;

$generator = $model
    ? ProductDescriptionGenerator::fromOpenRouter($apiKey, $model)
    : ProductDescriptionGenerator::fromOpenRouter($apiKey);

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

$modelName = $model ?? 'liquid/lfm-2 (default)';
echo "Generating product description via OpenRouter.ai (model: {$modelName})...\n\n";

$description = $generator->generate($technicalInfo);

echo $description . "\n";
