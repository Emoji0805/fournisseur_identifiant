<?php

namespace App\Controller;

use App\Service\EmailPinService;
use App\Entity\Utilisateurs;
use App\Service\TemporaryPinStorage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\MfaController;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;




class LoginController extends AbstractController
{
    private $entityManager;
    private $mfaController;
    private EmailPinService $emailPinService;
    private const MAX_ATTEMPTS = 6;
    private const RESET_TIME = 40; 
    private TemporaryPinStorage $pinStorage;
    private LoggerInterface $logger;
    private CacheInterface $cache;



    public function __construct(EntityManagerInterface $entityManager, EmailPinService $emailPinService, TemporaryPinStorage $pinStorage, LoggerInterface $logger, CacheInterface $cache)
    {
        $this->entityManager = $entityManager;
        $this->emailPinService = $emailPinService;
        $this->pinStorage = $pinStorage;
        $this->logger = $logger;
        $this->cache = $cache;
    }

    public function sendPin(Request $request, string $email)
    {
        $pin = $this->emailPinService->generatePin();  // Remplacez par votre méthode de génération de PIN
        $expiration = time() + 90;  // Expiration dans 90 secondes

        // Stocker le PIN dans le cache avec une expiration
        $this->pinStorage->storePin($email, $pin, $expiration);

        // Envoi du PIN par email
        
        $this->emailPinService->sendPinByEmail($email, $pin); // Remplacez par votre méthode d'envoi d'email
        return new JsonResponse(['redirect' => 'http://localhost:8080/cryptaka-1.0-SNAPSHOT/pages/frontoffice/validationPin.jsp']);
        // return new JsonResponse(['status' => 'success', 'message' => 'PIN envoyé']);
    }
    
    wawa
    #[Route('/api/login', name: 'login_utilisateur', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['email']) || !isset($data['motDePasse'])) {
            return $this->createErrorResponse(400, "Email et mot de passe requis");
        }

        $email = $data['email'];
        $motDePasseInput = $data['motDePasse'];

        if ($motDePasseInput === null) {
            return $this->createErrorResponse(400, "Le mot de passe est requis.");
        }

        $motDePasse = sha1($motDePasseInput);
        $ipAddress = $request->getClientIp();
        $cacheKey = "login_attempts_" . md5($ipAddress . $email);

        // Récupérer les tentatives actuelles
        $attempts = $this->cache->get($cacheKey, function (CacheItem $item) {
            $item->expiresAfter(self::RESET_TIME);
            return 0;
        });

        // Log des tentatives
        $this->logger->info("Tentatives actuelles", ['email' => $email, 'attempts' => $attempts]);

        if ($attempts >= self::MAX_ATTEMPTS) {
            return $this->createErrorResponse(429, "Trop de tentatives. Réessayez plus tard.");
        }

        $utilisateur = $this->entityManager->getRepository(Utilisateurs::class)->findOneBy(['email' => $email]);

        if (!$utilisateur || $utilisateur->getMotDePasse() !== $motDePasse) {
            $attempts++;
            $this->cache->delete($cacheKey); // Supprimer avant d'ajouter
            $this->cache->get($cacheKey, function (CacheItem $item) use ($attempts) {
                $item->set($attempts);
                $item->expiresAfter(self::RESET_TIME);
                return $attempts;
            });

            $remainingAttempts = self::MAX_ATTEMPTS - $attempts;
            return $this->createErrorResponse(401, "Email ou mot de passe incorrect. Il vous reste {$remainingAttempts} tentative(s).");
        }

        // Réinitialiser les tentatives en cas de succès
        $this->cache->delete($cacheKey);

        return $this->sendPin($request, $email);
    }

    #[Route('/mfa/verify-pin', name: 'mfa_verify_pin', methods: ['POST'])]
    public function verifyPin(Request $request, SessionInterface $session): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['pin'])) {
            return $this->createErrorResponse(400, "PIN et email requis");
        }
    
        $submittedPin = $data['pin'];
        $email = $session->get('email');
        $ipAddress = $request->getClientIp();
    
        // Generate a unique identifier for this user and IP address for tracking PIN attempts
        $identifier = "pin_attempts_" . md5($ipAddress . $email);
    
        // Get the current number of PIN attempts from the cache
        $attempts = $this->cache->get($identifier, function (CacheItem $item) {
            $item->expiresAfter(self::RESET_TIME); // Expire the cache after a certain time
            return 0; // Default value is 0 attempts
        });
    
        // Log the attempts
        $this->logger->info("Tentatives de PIN actuelles", ['email' => $email, 'attempts' => $attempts]);
    
        if ($attempts >= self::MAX_ATTEMPTS) {
            return $this->createErrorResponse(429, "Trop de tentatives de PIN. Veuillez réessayer plus tard.");
        }
    
        // Search for the email associated with the PIN
        $emailPin = $this->pinStorage->getEmailByPin($submittedPin);
    
        if (!$emailPin) {
            // Increment the attempts count in the cache if the PIN is incorrect
            $attempts++;
            $this->cache->delete($identifier); // Delete the previous value before setting the new one
            $this->cache->get($identifier, function (CacheItem $item) use ($attempts) {
                $item->set($attempts);
                $item->expiresAfter(self::RESET_TIME); // Cache expires after the defined time
                return $attempts;
            });
    
            // Return error response with remaining attempts
            $remainingAttempts = self::MAX_ATTEMPTS - $attempts;
            return $this->createErrorResponse(401, "PIN incorrect. Il vous reste {$remainingAttempts} tentative(s).");
        }
    
        // Retrieve stored PIN data
        $storedPinData = $this->pinStorage->getPin($emailPin);
    
        if ($storedPinData === null) {
            return $this->createErrorResponse(404, "Aucune donnée trouvée pour cet email.");
        }
    
        $storedPin = $storedPinData['pin'];
        $expiration = $storedPinData['expiration'];
    
        if (time() > $expiration) {
            $this->pinStorage->removePin($emailPin); // Remove expired PIN
            return $this->createErrorResponse(408, "Le PIN a expiré.");
        }
    
        // Check if the submitted PIN matches the stored one
        if ($storedPin && $submittedPin == $storedPin) {
            // Optionally remove the PIN from the storage after successful verification
            // $this->pinStorage->removePin($emailPin);
    
            // Retrieve the user and return a redirect response
            $utilisateur = $this->entityManager->getRepository(Utilisateurs::class)->findOneBy(['email' => $emailPin]);
    
            if ($utilisateur) {
                $userId = $utilisateur->getIdUtilisateur();
                return new JsonResponse(['redirect' => 'http://localhost:8080/cryptaka-1.0-SNAPSHOT/acceuilServlet?id=' . $userId]);
            }
        }
    
        return $this->createErrorResponse(401, "PIN incorrect.");
    }
    

    #[Route('/api/reset-session', name: 'reset_session', methods: ['POST'])]
    public function resetSession(Request $request, SessionInterface $session): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $session->get('email');
        $ipAddress = $request->getClientIp(); 

        $identifier = "login_attempts_" . md5($ipAddress . $email);

        if ($session->has($identifier)) {
            $session->remove($identifier);

            return new JsonResponse([
                'status' => 'succés',
                'message' => "La session pour l'utilisateur avec l'email '{$email}' et l'IP '{$ipAddress}' a été réinitialisée.",
                'error' => null,
            ], 200);
        }
        
        return $this->createErrorResponse(404, "Aucune session trouvée pour l'utilisateur avec l'email '{$email}' et l'IP '{$ipAddress}'.");
    }

    /**
     * Fonction utilitaire pour créer une réponse d'erreur standardisée.
     */
    private function createErrorResponse(int $statusCode, string $message): JsonResponse
    {
        return new JsonResponse([
            'status' => 'error',
            'message' => $message,
        ], $statusCode);
    }
    

    #[Route('/api/checkSession', name: 'checkSession', methods: ['GET'])]
    public function checkSession(SessionInterface $session): JsonResponse
    {
        $userId = $session->get('user_id');
        $expiresAt = $session->get('expires_at');

        if (!$userId || time() > $expiresAt) {
            $session->clear();

            return new JsonResponse(['message' => 'Session expired. Go login !']);
        }

        return new JsonResponse(['message' => 'Session encore active']);
    }
}
