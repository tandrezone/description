<?php

/**
 * Example: Generate descriptions from random / arbitrary technical information.
 *
 * This example demonstrates that the package handles any key-value structure —
 * keys do not need to follow a fixed schema. It uses local Ollama so no API
 * token is required, but it can easily be swapped for GitHub Models by
 * replacing `ProductDescriptionGenerator::fromOllama()` with:
 *
 *   new ProductDescriptionGenerator(getenv('GITHUB_TOKEN'))
 *
 * Run:
 *   php examples/random-tech-info.php
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Tandrezone\Description\ProductDescriptionGenerator;

// --- Helpers -----------------------------------------------------------------

function printSeparator(string $title): void
{
    echo "\n" . str_repeat('=', 60) . "\n";
    echo "  {$title}\n";
    echo str_repeat('=', 60) . "\n\n";
}

// --- Generator ---------------------------------------------------------------

$generator = ProductDescriptionGenerator::fromOllama();

// --- Product 1: Smart TV -----------------------------------------------------

printSeparator('Product 1: Smart TV');

$smartTv = [
    'brand'            => 'VisionTech',
    'screen_size'      => '55 inches',
    'resolution'       => '4K UHD (3840×2160)',
    'refresh_rate'     => '120Hz',
    'smart_platform'   => 'Android TV 13',
    'hdr_support'      => ['HDR10', 'Dolby Vision', 'HLG'],
    'connectivity'     => ['4× HDMI 2.1', '3× USB', 'Wi-Fi 6', 'Bluetooth 5.0'],
    'audio'            => '2.1ch 40W Dolby Atmos',
];

echo $generator->generate($smartTv) . "\n";

// --- Product 2: Running shoes ------------------------------------------------

printSeparator('Product 2: Running Shoes');

$runningShoes = [
    'brand'          => 'SwiftStep',
    'model'          => 'AirRun Pro X',
    'upper_material' => 'Engineered mesh with TPU overlays',
    'midsole'        => 'Dual-density foam with carbon-fibre plate',
    'outsole'        => 'Continental rubber',
    'drop'           => '8mm',
    'weight'         => '210g (US Men\'s 9)',
    'best_for'       => ['road running', 'racing', 'tempo training'],
    'available_sizes'=> 'US 6 – 15 (half sizes included)',
];

echo $generator->generate($runningShoes) . "\n";

// --- Product 3: Coffee machine -----------------------------------------------

printSeparator('Product 3: Coffee Machine');

$coffeeMachine = [
    'brand'              => 'BrewMaster',
    'type'               => 'Bean-to-cup espresso machine',
    'pressure'           => '19-bar pump',
    'grinder'            => 'Integrated ceramic burr grinder, 12 settings',
    'milk_system'        => 'Automatic milk frother',
    'water_tank'         => '1.8 L removable',
    'bean_hopper'        => '250g',
    'drinks'             => ['espresso', 'lungo', 'cappuccino', 'latte', 'flat white'],
    'connectivity'       => 'Wi-Fi + Bluetooth app control',
    'dimensions_cm'      => '25 W × 40 D × 38 H',
];

echo $generator->generate($coffeeMachine) . "\n";
