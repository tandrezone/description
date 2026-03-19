<?php

/**
 * Example: Generate a product description using a local Ollama instance.
 *
 * Requirements:
 *   - Ollama installed and running: https://ollama.com
 *   - The desired model pulled locally, e.g.:
 *       ollama pull lfm2
 *   - Run this script:
 *       php examples/ollama.php
 *
 * By default the `lfm2` model is used via http://localhost:11434/v1.
 * Pass a different model name as the first argument to override, e.g.:
 *       php examples/ollama.php llama3.2
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Tandrezone\Description\ProductDescriptionGenerator;

$model = $argv[1] ?? null;

$generator = $model
    ? ProductDescriptionGenerator::fromOllama($model)
    : ProductDescriptionGenerator::fromOllama();

$technicalInfo = [
    'brand'        => 'AudioMax',
    'type'         => 'Over-ear headphones',
    'driver_size'  => '40mm dynamic driver',
    'frequency'    => '20Hz – 20kHz',
    'connectivity' => ['Bluetooth 5.3', '3.5mm jack'],
    'battery_life' => '30 hours',
    'noise_cancelling' => 'Active Noise Cancellation (ANC)',
    'weight'       => '250g',
];

$modelName = $model ?? 'lfm2 (default)';
echo "Generating product description via local Ollama (model: {$modelName})...\n\n";

$description = $generator->generate($technicalInfo);

echo $description . "\n";
