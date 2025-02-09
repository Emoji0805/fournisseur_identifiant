<?php

namespace App\Controller;

use App\Entity\Utilisateurs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Service\EmailPinService;


class SendPIN extends AbstractController
{
    private EmailPinService $emailPinService;

    public function __construct(EmailPinService $emailPinService)
    {
        $this->emailPinService = $emailPinService;
    }

    #[Route('/mfa/send-pin', name: 'mfa_send_pin')]
    public function sendPin(SessionInterface $session): Response
    {
        $email = 'mamihery.rabiazamaholy@gmail.com'; 

        $pin = $this->emailPinService->generatePin();
        $session->set('mfa_pin', $pin);
        $session->set('mfa_expiration', time() + 90);

        $this->emailPinService->sendPinByEmail($email, $pin);
        // dump($pin);

        return $this->render('mfa/enter_pin.html.twig', [
            'message' => 'Un code PIN a été envoyé à votre adresse email.',
            'error' => null,
        ]);
    }

}
