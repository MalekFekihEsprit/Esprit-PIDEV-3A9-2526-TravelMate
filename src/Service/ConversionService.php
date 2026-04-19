<?php
namespace App\Service;

class ConversionService
{
    public function __construct(
        private OpenRouterClient $client,
        private string $apiKey,
        private string $model
    ) {}

    public function convert(float $montant, string $from, string $to): string
    {
        return $this->client->chat($this->apiKey, $this->model, [
            ['role' => 'system', 'content' => 'Tu es un convertisseur de devises. Donne uniquement le résultat numérique converti et le taux utilisé, en français, en 1-2 phrases.'],
            ['role' => 'user',   'content' => "Convertis $montant $from en $to avec le taux de change actuel estimé."],
        ]);
    }
}