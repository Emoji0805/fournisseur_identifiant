<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\MailService;
use App\Entity\Utilisateurs;
use App\Service\EmailValidatorService;
use App\Repository\UtilisateursRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;


class MailController extends AbstractController
{
    private $mailService;
    private $emailValidatorService;
    
    public function __construct(MailService $mailService, EmailValidatorService $emailValidatorService)
    {
        $this->mailService = $mailService;
        $this->emailValidatorService = $emailValidatorService;
    }
    
    #[Route('/api/send_recovery_mail', name: 'send_recovery_mail', methods: ['POST'])] //mety
    public function sendRcvMail(SessionInterface $session): Response
    {
        $to = $session->get('email');
        $subject = 'Mail de recuperation de tentatives';
        $htmlContent = '<h1>Restitution de tentatives</h1><p>Restituez vos tentatives de connexion en cliquant sur ce lien : <form action="http://localhost:8000/api/reset-session" method="post"><input type="submit" value="click here"></form></p>';
    
        $result = $this->mailService->sendMail($to, $subject, $htmlContent);
    
        return new Response($result);
    }
    
    #[Route('/api/send-mail', name: 'send_mail', methods: ['POST'])] //mety
    public function sendMail(): Response
    {   
        $to = 'isaiajoellenirina@gmail.com';
        $subject = 'Test Symfony Mail';
        $htmlContent = '<h1>Bonjour</h1><p>Ceci est un test d\'e-mail HTML via Symfony.</p>';

        $result = $this->mailService->sendMail($to, $subject, $htmlContent);

        return new Response($result);
    }
    #[Route('/api/send-mail-depot/{email}/{montant}', name: 'send_mail_depot', methods: ['POST'])]
    public function sendMailDepot(string $email, string $montant): Response
    {   
        $to = $email;
        $subject = 'Confirmation depot';
        $htmlContent = '<h1>Bonjour</h1><p>Confirmez votre demande de dépot de '.$montant.' <a href="">cliquez ici</a>.</p>';
    
        $result = $this->mailService->sendMail($to, $subject, $htmlContent);
    
        return new Response($result);
    }
    #[Route('/api/send-mail-depot/{email}/{montant}', name: 'send_mail_depot', methods: ['POST'])]
    public function sendMailRetrait(string $email, string $montant): Response
    {   
        $to = $email;
        $subject = 'Confirmation retrait';
        $htmlContent = '<h1>Bonjour</h1><p>Confirmez votre demande de retrait de '.$montant.' <a href="">cliquez ici</a>.</p>';
    
        $result = $this->mailService->sendMail($to, $subject, $htmlContent);
    
        return new Response($result);
    }
    

    

    
    #[Route('/api/test-mail', name: 'test_mail', methods: ['POST'])]
    public function test(Request $request,UtilisateursRepository $utilisateursRepository)
    {
        $data = json_decode($request->getContent(), true);



        $email = $data['email'] ?? null;
        $nom = $data['nom'] ?? null;
        $mdp = $data['mdp'] ?? null;
        

        // Validation de l'email
        $isValidEmail = $this->emailValidatorService->validateEmail($email);

        
        if ($isValidEmail) { 
            $utilisateur = new Utilisateurs();
            // $utilisateur->setIdrole(1);
            $utilisateur->setNom($nom);
            $utilisateur->setEmail($email);
            $utilisateur->setMotDePasse(sha1($mdp));
            
            $utilisateursRepository->insert($utilisateur);
            
            return new JsonResponse(['redirect' => 'http://localhost:8080/cryptaka-1.0-SNAPSHOT/pages/frontoffice']);

        }else{
            return new JsonResponse(['message' => 'Mail Invalide !']);
        }
    
        // return $this->json([
        //     'status' => 'succés',
        //     'message'   =>"reponse recue'{ $isValidEmail}'",
        //     'error' => null,
        // ]);
    }
    
    
    #[Route('/dash', name: 'dash')]
    public function dash(): Response
    {
        return $this->render('home.html.twig');
    }
}
