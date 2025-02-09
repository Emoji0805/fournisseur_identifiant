<?php

namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class TemporaryPinStorage
{
    private CacheInterface $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function storePin(string $email, string $pin, int $expiration): void
    {
        // Hacher l'email pour générer une clé sûre
        $hashedEmail = sha1($email);  // Utiliser sha1 ou md5 si vous préférez
    
        // Stocker le PIN dans le cache
        $this->cache->get($hashedEmail, function (ItemInterface $item) use ($pin, $expiration) {
            $item->expiresAfter($expiration - time());  // Expiration en fonction du temps restant
            return [
                'pin' => $pin,
                'expiration' => $expiration,
            ];
        });
    
        // Stocker également le PIN pour rechercher l'email
        $this->cache->get($pin, function (ItemInterface $item) use ($email, $expiration) {
            $item->expiresAfter($expiration - time());  // Expiration en fonction du temps restant
            return [
                'email' => $email,
                'expiration' => $expiration,
            ];
        });
    }
    
    

    public function getPin(string $email): ?array
{
    // Hacher l'email pour générer une clé sûre
    $hashedEmail = sha1($email);  // Utiliser sha1 ou md5 si vous préférez

    // Récupérer le PIN depuis le cache
    $pinData = $this->cache->get($hashedEmail, function (ItemInterface $item) {
        return null; // Retourner null si le PIN n'est pas trouvé
    });

    // Ajouter un log ou un message pour déboguer
    if ($pinData === null) {
        error_log("PIN non trouvé pour l'email: " . $email);
    } else {
        error_log("PIN trouvé pour l'email: " . $email . " - Données: " . print_r($pinData, true));
    }

    return $pinData;
}


    
    

    public function getEmailByPin(string $pin)
    {
        return $this->cache->get($pin, function (ItemInterface $item) {
            return null;  // Retourne null si le PIN n'est pas trouvé
        })['email'] ?? null;
    }

    public function removePin(string $email): void
    {
        $encodedEmail = urlencode($email); // Encode l'email avant de supprimer le PIN
        $pinData = $this->cache->get($encodedEmail, function (ItemInterface $item) {
            return null; // Retourner null si le PIN n'est pas trouvé
        });

        if ($pinData) {
            $pin = $pinData['pin'];
            // Supprimer le PIN par email et le PIN par sa propre clé
            $this->cache->delete($encodedEmail);
            $this->cache->delete($pin);
        }
    }

    public function clearExpiredPins(): void
    {
        // Le cache Symfony gère automatiquement l'expiration des éléments
    }
}   