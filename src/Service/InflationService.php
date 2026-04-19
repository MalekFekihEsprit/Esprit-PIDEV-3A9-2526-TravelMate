<?php

namespace App\Service;

class InflationService
{
    public function __construct(
        private OpenRouterClient $client,
        private string $apiKey,
        private string $model
    ) {}

    public function adjustForInflation(float $amount, string $yearFrom, string $yearTo, string $currency): string
    {
        return $this->client->chat($this->apiKey, $this->model, [
            ['role' => 'system', 'content' => 'Tu es un expert en économie et inflation. Réponds en français, de manière précise et concise.'],
            ['role' => 'user', 'content' => "Ajuste le montant de $amount $currency de l'année $yearFrom à l'année $yearTo en tenant compte de l'inflation estimée."],
        ]);
    }
}