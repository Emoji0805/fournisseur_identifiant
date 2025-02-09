<?php

namespace App\Controller;

use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CheckSession extends AbstractController
{
    private $entityManager;
    private const MAX_ATTEMPTS = 6;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/checkSession', name: 'checkSession', methods: ['GET'])]
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
