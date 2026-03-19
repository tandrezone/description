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

### Local Ollama

No API token is needed for a local [Ollama](https://ollama.com) instance. Use the `fromOllama()` factory:

```bash
# pull the model once
ollama pull lfm2
```

```php
use Tandrezone\Description\ProductDescriptionGenerator;

// default: model=lfm2, endpoint=http://localhost:11434/v1
$generator = ProductDescriptionGenerator::fromOllama();

// or choose a different model
$generator = ProductDescriptionGenerator::fromOllama('llama3.2');

$description = $generator->generate([
    'brand'        => 'AudioMax',
    'type'         => 'Over-ear headphones',
    'battery_life' => '30 hours',
    'connectivity' => ['Bluetooth 5.3', '3.5mm jack'],
]);

echo $description;
```

## Examples

Ready-to-run scripts are in the [`examples/`](examples/) directory:

| Script | Description |
|--------|-------------|
| [`examples/github-models.php`](examples/github-models.php) | Generate a description using GitHub Models (LFM-2). Requires `GITHUB_TOKEN` env var. |
| [`examples/ollama.php`](examples/ollama.php) | Generate a description using local Ollama. Accepts an optional model name argument. |
| [`examples/random-tech-info.php`](examples/random-tech-info.php) | Three products with arbitrary specs — demonstrates the flexible key-value input format. |

```bash
# GitHub Models
export GITHUB_TOKEN=your_token_here
php examples/github-models.php

# Ollama (default model: lfm2)
php examples/ollama.php

# Ollama with a custom model
php examples/ollama.php llama3.2

# Random tech info showcase (uses Ollama)
php examples/random-tech-info.php
```

## Running tests

```bash
composer install
./vendor/bin/phpunit tests/
```

## License

MIT
