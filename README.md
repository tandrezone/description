# tandrezone/description

A PHP Composer package that generates product descriptions from technical information using the **LFM-2** (Liquid Foundation Model 2) via the GitHub Models API.

## Requirements

- PHP 8.1+
- Composer
- A GitHub personal access token (PAT) with the `models` scope

## Installation

```bash
composer require tandrezone/description
```

## Usage

```php
use Tandrezone\Description\ProductDescriptionGenerator;

$apiKey = getenv('GITHUB_TOKEN'); // Your GitHub PAT with models scope

$generator = new ProductDescriptionGenerator($apiKey);

// Provide any technical product info as a key-value array
$technicalInfo = [
    'brand'        => 'TechBrand',
    'model'        => 'ProBook X1',
    'cpu'          => 'Intel Core i7-1355U',
    'ram'          => '16GB DDR5',
    'storage'      => '512GB NVMe SSD',
    'display'      => '14-inch FHD IPS, 400 nits',
    'battery_life' => 'Up to 12 hours',
    'weight'       => '1.35 kg',
    'os'           => 'Windows 11 Pro',
];

$description = $generator->generate($technicalInfo);

echo $description;
```

### Custom model or API endpoint

By default the package uses `liquid/lfm-2` via GitHub Models (`https://models.github.ai/inference`). Both can be overridden:

```php
$generator = new ProductDescriptionGenerator(
    apiKey:  $apiKey,
    model:   'liquid/lfm-2',           // any GitHub Models model ID
    baseUri: 'https://models.github.ai/inference'
);
```

### Technical info format

The `generate()` method accepts an associative array of any shape. Keys are automatically formatted (underscores and hyphens replaced by spaces, first letter of each word capitalised). Array values are joined with a comma.

```php
$technicalInfo = [
    'available_colors' => ['red', 'blue', 'green'],
    'max-speed'        => '120 km/h',
    'weight_kg'        => 1.2,
];
```

## Running tests

```bash
composer install
./vendor/bin/phpunit tests/
```

## License

MIT
