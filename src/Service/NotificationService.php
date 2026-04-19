<?php
namespace App\Service;

class NotificationService
{
    public function __construct(
        private OpenRouterClient $client,
        private string $apiKey,
        private string $model
    ) {}

    public function analyzeAlert(string $libelle, float $pct, float $restant, string $devise): string
    {
        return $this->client->chat($this->apiKey, $this->model, [
            ['role' => 'system', 'content' => 'Tu es un assistant financier de voyage. Réponds en français, en 2 phrases maximum, de manière concise et utile.'],
            ['role' => 'user',   'content' => "Le budget \"$libelle\" a atteint $pct% de consommation. Il reste $restant $devise. Donne un conseil rapide."],
        ]);
    }
}