<?php

namespace App\Controller;

use App\Repository\UtilisateursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController
{
    #[Route('/api/loginSession', name: 'login', methods: ['POST'])]
    public function login(Request $request, UtilisateursRepository $utilisateursRepository, SessionInterface $session): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Invalid credentials',
                'error' => [
                    'details' => "Check that the things that you write are correct."
                ]
            ], 400);
        }

        $user = $utilisateursRepository->findByEmailAndPassword($email, $password);

        if (!$user) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'User not found',
                'error' => [
                    'details' => "Check that the things that you write are correct."
                ]
            ], 400);        }

        $session->set('user_id', $user->getIdUtilisateur());
        $session->set('expires_at', time() + 20);

        return new JsonResponse([
            'status' => 'succés',
            'message' => 'Logged in succésfully',
            'error' => null]);
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
