<?php

declare(strict_types=1);

namespace Tandrezone\Description;

use OpenAI;
use OpenAI\Contracts\ClientContract;

class ProductDescriptionGenerator
{
    private const DEFAULT_MODEL = 'liquid/lfm-2';
    private const DEFAULT_BASE_URI = 'https://models.github.ai/inference';
    private const OLLAMA_BASE_URI = 'http://localhost:11434/v1';
    private const OLLAMA_DEFAULT_MODEL = 'lfm2';
    private const OPENROUTER_BASE_URI = 'https://openrouter.ai/api/v1';
    private const OPENROUTER_DEFAULT_MODEL = 'liquid/lfm-2';

    private ClientContract $client;
    private string $model;

    public function __construct(
        string $apiKey,
        string $model = self::DEFAULT_MODEL,
        string $baseUri = self::DEFAULT_BASE_URI
    ) {
        $this->model = $model;
        $this->client = OpenAI::factory()
            ->withApiKey($apiKey)
            ->withBaseUri($baseUri)
            ->make();
    }

    /**
     * Create a generator configured to use a local Ollama instance.
     *
     * @param string $model   The Ollama model name (as shown by `ollama list`)
     * @param string $baseUri The Ollama OpenAI-compatible endpoint
     */
    public static function fromOllama(
        string $model = self::OLLAMA_DEFAULT_MODEL,
        string $baseUri = self::OLLAMA_BASE_URI
    ): static {
        return new static('ollama', $model, $baseUri);
    }

    /**
     * Create a generator configured to use OpenRouter.ai.
     *
     * @param string $apiKey  Your OpenRouter API key (https://openrouter.ai/keys)
     * @param string $model   The OpenRouter model ID (see https://openrouter.ai/models)
     * @param string $baseUri The OpenRouter API endpoint
     */
    public static function fromOpenRouter(
        string $apiKey,
        string $model = self::OPENROUTER_DEFAULT_MODEL,
        string $baseUri = self::OPENROUTER_BASE_URI
    ): static {
        return new static($apiKey, $model, $baseUri);
    }

    /**
     * Generate a product description from technical information.
     *
     * @param array<string, mixed> $technicalInfo Key-value pairs of technical product attributes
     * @return string The generated product description
     */
    public function generate(array $technicalInfo): string
    {
        $prompt = $this->buildPrompt($technicalInfo);

        $response = $this->client->chat()->create([
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a professional product copywriter. Generate clear, engaging, and accurate product descriptions based on technical specifications provided. The description should be suitable for an e-commerce website and highlight the key features and benefits.',
                ],
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
        ]);

        return trim($response->choices[0]->message->content ?? '');
    }

    /**
     * Build a prompt string from technical info array.
     *
     * @param array<string, mixed> $technicalInfo
     */
    private function buildPrompt(array $technicalInfo): string
    {
        $specs = [];
        foreach ($technicalInfo as $key => $value) {
            $formattedKey = ucwords(str_replace(['_', '-'], ' ', (string) $key));
            $formattedValue = is_array($value) ? implode(', ', $value) : (string) $value;
            $specs[] = "- {$formattedKey}: {$formattedValue}";
        }

        $specsList = implode("\n", $specs);

        return "Please write a product description based on the following technical specifications:\n\n{$specsList}";
    }
}
