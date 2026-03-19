<?php

declare(strict_types=1);

namespace Tandrezone\Description\Tests;

use OpenAI\Responses\Chat\CreateResponse;
use OpenAI\Testing\ClientFake;
use PHPUnit\Framework\TestCase;
use Tandrezone\Description\ProductDescriptionGenerator;

class ProductDescriptionGeneratorTest extends TestCase
{
    public function testGenerateReturnsTrimmedDescription(): void
    {
        $fakeClient = new ClientFake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => '  A high-performance laptop for professionals.  ',
                        ],
                        'finish_reason' => 'stop',
                        'index' => 0,
                    ],
                ],
            ]),
        ]);

        $generator = $this->createGeneratorWithFakeClient($fakeClient);

        $result = $generator->generate([
            'brand' => 'TechBrand',
            'model' => 'ProBook X1',
            'cpu' => 'Intel Core i7',
            'ram' => '16GB',
        ]);

        $this->assertSame('A high-performance laptop for professionals.', $result);
    }

    public function testGenerateWithRandomTechnicalInfo(): void
    {
        $fakeClient = new ClientFake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Experience the future with this cutting-edge device.',
                        ],
                        'finish_reason' => 'stop',
                        'index' => 0,
                    ],
                ],
            ]),
        ]);

        $generator = $this->createGeneratorWithFakeClient($fakeClient);

        $technicalInfo = [
            'weight' => '1.2kg',
            'battery_life' => '12 hours',
            'display' => '14-inch FHD',
            'storage' => ['256GB SSD', '1TB HDD'],
        ];

        $result = $generator->generate($technicalInfo);

        $this->assertNotEmpty($result);
        $this->assertIsString($result);
    }

    public function testGenerateWithEmptyTechnicalInfo(): void
    {
        $fakeClient = new ClientFake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'A versatile product.',
                        ],
                        'finish_reason' => 'stop',
                        'index' => 0,
                    ],
                ],
            ]),
        ]);

        $generator = $this->createGeneratorWithFakeClient($fakeClient);

        $result = $generator->generate([]);

        $this->assertIsString($result);
    }

    public function testGenerateSendsCorrectModelAndMessages(): void
    {
        $fakeClient = new ClientFake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Product description.',
                        ],
                        'finish_reason' => 'stop',
                        'index' => 0,
                    ],
                ],
            ]),
        ]);

        $generator = $this->createGeneratorWithFakeClient($fakeClient);
        $generator->generate(['color' => 'red', 'size' => 'large']);

        $fakeClient->chat()->assertSent(function (string $method, array $parameters): bool {
            return $method === 'create'
                && $parameters['model'] === 'liquid/lfm-2'
                && count($parameters['messages']) === 2
                && $parameters['messages'][0]['role'] === 'system'
                && $parameters['messages'][1]['role'] === 'user'
                && str_contains($parameters['messages'][1]['content'], 'Color: red')
                && str_contains($parameters['messages'][1]['content'], 'Size: large');
        });
    }

    public function testGenerateWithArrayValues(): void
    {
        $fakeClient = new ClientFake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Multi-color product description.',
                        ],
                        'finish_reason' => 'stop',
                        'index' => 0,
                    ],
                ],
            ]),
        ]);

        $generator = $this->createGeneratorWithFakeClient($fakeClient);

        $result = $generator->generate([
            'available_colors' => ['red', 'blue', 'green'],
        ]);

        $fakeClient->chat()->assertSent(function (string $method, array $parameters): bool {
            return str_contains($parameters['messages'][1]['content'], 'red, blue, green');
        });

        $this->assertIsString($result);
    }

    public function testFromOllamaCreatesInstanceWithCorrectDefaults(): void
    {
        $fakeClient = new ClientFake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Locally generated description.',
                        ],
                        'finish_reason' => 'stop',
                        'index' => 0,
                    ],
                ],
            ]),
        ]);

        $generator = ProductDescriptionGenerator::fromOllama();
        $this->injectFakeClient($generator, $fakeClient);

        $result = $generator->generate(['type' => 'monitor', 'size' => '27-inch']);

        $fakeClient->chat()->assertSent(function (string $method, array $parameters): bool {
            return $method === 'create'
                && $parameters['model'] === 'lfm2';
        });

        $this->assertSame('Locally generated description.', $result);
    }

    public function testFromOllamaAcceptsCustomModelName(): void
    {
        $fakeClient = new ClientFake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'role' => 'assistant',
                            'content' => 'Custom model description.',
                        ],
                        'finish_reason' => 'stop',
                        'index' => 0,
                    ],
                ],
            ]),
        ]);

        $generator = ProductDescriptionGenerator::fromOllama('llama3.2');
        $this->injectFakeClient($generator, $fakeClient);

        $generator->generate(['brand' => 'Acme']);

        $fakeClient->chat()->assertSent(function (string $method, array $parameters): bool {
            return $parameters['model'] === 'llama3.2';
        });
    }


    /**
     * Creates a ProductDescriptionGenerator and injects a fake OpenAI client via reflection.
     */
    private function createGeneratorWithFakeClient(ClientFake $fakeClient): ProductDescriptionGenerator
    {
        $generator = new ProductDescriptionGenerator('fake-api-key');
        $this->injectFakeClient($generator, $fakeClient);

        return $generator;
    }

    /**
     * Injects a fake OpenAI client into an existing generator instance via reflection.
     */
    private function injectFakeClient(ProductDescriptionGenerator $generator, ClientFake $fakeClient): void
    {
        $reflection = new \ReflectionClass($generator);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($generator, $fakeClient);
    }
}
