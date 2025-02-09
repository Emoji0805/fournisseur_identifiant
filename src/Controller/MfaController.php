<?php

namespace App\Controller;

use App\Service\EmailPinService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class MfaController extends AbstractController
{
    private EmailPinService $emailPinService;

    public function __construct(EmailPinService $emailPinService)
    {
        $this->emailPinService = $emailPinService;
    }

    
    public function sendPin(SessionInterface $session, string $email): Response
        {
        $email = $email; 

        // Générer et stocker le PIN
        $pin = $this->emailPinService->generatePin();
        $session->set('mfa_pin', $pin);
        $session->set('mfa_expiration', time() + 90);

        // Envoyer le PIN par email
        $this->emailPinService->sendPinByEmail($email, $pin);

        return $this->redirect('http://localhost:8080/crypto-1.0-SNAPSHOT/enter_pin.jsp');
        
    }

}

// composer require orm 

//  composer require doctrine 

// composer require phpmailer/phpmailer  

// composer require egulias/email-validator

// composer require symfony/http-client

// composer require symfony/twig-bundle
