<?php

namespace App\Repository;

use App\Entity\Utilisateurs;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Utilisateurs>
 *
 * @method Utilisateurs|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilisateurs|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilisateurs[]    findAll()
 * @method Utilisateurs[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateurs::class);
    }

    // Exemple : Trouver tous les utilisateurs par rôle
    // public function findByRole(int $idrole): array
    // {
    //     return $this->createQueryBuilder('u')
    //         ->andWhere('u.idrole = :idrole')
    //         ->setParameter('idrole', $idrole)
    //         ->orderBy('u.idUtilisateur', 'ASC')
    //         ->getQuery()
    //         ->getResult();
    // }

    // Insertion des utilisateurs
    public function insert(Utilisateurs $utilisateur): void
    {
        $entityManager = $this->getEntityManager();

        $entityManager->persist($utilisateur);

        $entityManager->flush();
    }
}

