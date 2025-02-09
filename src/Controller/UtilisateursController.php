<?php

namespace App\Controller;

use App\Repository\UtilisateursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface; 
use App\Entity\Utilisateurs;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class UtilisateursController extends AbstractController
{
    #[Route('/api/utilisateurs', name: 'list_utilisateurs', methods: ['GET'])]
    public function listUtilisateurs(UtilisateursRepository $utilisateursRepository): JsonResponse  
    {
        // Récupérer tous les utilisateurs
        $utilisateurs = $utilisateursRepository->findAll();

        // Vérifier s'il y a des utilisateurs
        if (empty($utilisateurs)) {
            return $this->json([
                'status' => 'error',
                'message' => 'Aucun utilisateur trouvé dans la base de données.',
                'error' => [
                    'code' => JsonResponse::HTTP_NOT_FOUND,
                    'details' => 'Aucun enregistrement d\'utilisateurs n\'a été trouvé.',
                ],
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        // Préparer les données à retourner
        $data = array_map(function ($utilisateur) {
            return [
                'id' => $utilisateur->getIdUtilisateur(),
                'nom' => $utilisateur->getNom(),
                'email' => $utilisateur->getEmail(),
            ];
        }, $utilisateurs);

        // Répondre avec succés
        return $this->json([
            'status' => 'succés',
            'data' => $data,
            'error' => null,
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/api/utilisateurs/{id}', name: 'update_utilisateur', methods: ['PUT'])]
    public function updateUtilisateur(  
        int $id,
        Request $request,
        EntityManagerInterface $em
    ): JsonResponse {
        $user = $em->getRepository(Utilisateurs::class)->find($id);

        if (!$user) {
            return $this->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['nom'])) {
            $user->setNom($data['nom']);
        }

        if (isset($data['motDePasse'])) {
            $user->setMotDePasse($data['motDePasse']);
        }

        $em->persist($user);
        $em->flush();

        return $this->json([
            'status' => 'succés',
            'message' => 'Utilisateur mis à jour avec succés',
            'data' => [
                'id' => $user->getIdUtilisateur(),
                'nom' => $user->getNom(),
                'email' => $user->getEmail(),
            ],
            'error' => null,
        ], 200);
    }
}

