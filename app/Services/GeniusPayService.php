<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeniusPayService
{
    protected string $baseUrl;
    protected string $publicKey;
    protected string $secretKey;

    public function __construct()
    {
        $this->baseUrl = config('services.geniuspay.base_url', 'https://sandbox.geniuspay.io/api/v1');
        $this->publicKey = config('services.geniuspay.public_key');
        $this->secretKey = config('services.geniuspay.secret_key');
    }

    /**
     * Crée une transaction sur GeniusPay.
     */
    public function createTransaction(array $data): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($this->baseUrl . '/transactions', [
            'amount' => $data['amount'] * 100, // GeniusPay utilise les centimes
            'currency' => 'XAF',
            'description' => $data['description'] ?? 'Paiement Campus360',
            'customer_email' => $data['email'] ?? '',
            'customer_name' => $data['name'] ?? '',
            'customer_phone' => $data['phone'] ?? '',
            'reference' => $data['reference'] ?? null,
            'callback_url' => route('payments.callback'),
            'return_url' => route('payments.index'),
            'cancel_url' => route('payments.index'),
        ]);

        if ($response->failed()) {
            Log::error('GeniusPay API Error: ' . $response->body());
            throw new \Exception('Erreur lors de la création de la transaction GeniusPay: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Récupère le statut d'une transaction.
     */
    public function getTransactionStatus(string $transactionId): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->get($this->baseUrl . '/transactions/' . $transactionId);

        if ($response->failed()) {
            Log::error('GeniusPay API Error (get status): ' . $response->body());
            throw new \Exception('Erreur lors de la récupération du statut de la transaction.');
        }

        return $response->json();
    }

    /**
     * Vérifie la signature du callback (sécurité).
     */
    public function verifySignature(array $payload, string $signature): bool
    {
        // À adapter selon la documentation GeniusPay
        $computedSignature = hash_hmac('sha256', json_encode($payload), $this->secretKey);
        return hash_equals($computedSignature, $signature);
    }
}
