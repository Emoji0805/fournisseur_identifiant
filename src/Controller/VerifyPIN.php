<?php

namespace App\Controller;

use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\EmailPinService;

class VerifyPIN extends AbstractController
{
    private EmailPinService $emailPinService;
    private const MAX_ATTEMPTS = 6;

    public function __construct(EmailPinService $emailPinService)
    {
        $this->emailPinService = $emailPinService;
    }

    // #[Route('/mfa/verify-pin', name: 'mfa_verify_pin', methods: ['POST'])]
    // public function verifyPin(Request $request, SessionInterface $session): JsonResponse
    // {
    //     $data = json_decode($request->getContent(), true);
    //     $submittedPin = $data['pin'] ?? null;

    //     $storedPin = $session->get('mfa_pin');
    //     $expiration = $session->get('mfa_expiration');

    //     if ($storedPin && (string)$submittedPin === (string)$storedPin && time() <= $expiration) {
    //         $session->remove('mfa_pin');
    //         $session->remove('mfa_expiration');

    //         return new JsonResponse([
    //             'status' => 'success',
    //             'message' => 'IP réussie',
    //         ], 200); 
    //     }

    //     //tsy metyyy-------------------
    //     $email = $session->get('email');
    //     $ipAddress = $request->getClientIp(); 

    //     $identifier = "login_attempts_" . md5($ipAddress . $email);

    //     $attempts = $session->get($identifier, 0);

    //     if ($attempts >= self::MAX_ATTEMPTS) {
    //         return $this->createErrorResponse(429, "Trop de tentatives de PIN. Veuillez réessayer plus tard.", $submittedPin);
    //     }

    //     $attempts++;
    //     $session->set($identifier, $attempts);

    //     $remainingAttempts = self::MAX_ATTEMPTS - $attempts;
      
        

    //     return $this->createErrorResponse(401, "PIN incorrect. Il vous reste {$remainingAttempts} tentative(s).", $submittedPin);
    // }

    private function createErrorResponse(int $statusCode, string $message): JsonResponse
    {
        return new JsonResponse([
            'status' => 'error',
            'message' => $message,
            'error' => [
                'code' => $statusCode,
                'details' => "Check that the things that you write are correct."
            ]
        ], $statusCode);
    }
}
