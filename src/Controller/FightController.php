<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\UserChampion;
use App\Entity\User;
use App\Entity\Fight;

#[Route('/api')]
class FightController extends AbstractController
{
    #[Route('/fight', name: 'app_fight', methods:['POST'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();

        // Récupérez l'utilisateur actuel
        $userChampion = $em
            ->getRepository(UserChampion::class)
            ->findOneBy(['user' => $user->getId()]);

        // Récupérez tous les utilisateurs
        $allUsers = $em->getRepository(User::class)->findAll();

        // Supprimez l'utilisateur actuel de la liste des utilisateurs
        $remainingUsers = array_filter($allUsers, function ($u) use ($user) {
            return $u->getId() !== $user->getId();
        });

        // Sélectionnez un ennemi aléatoire parmi les utilisateurs restants
        $ennemy = $remainingUsers[array_rand($remainingUsers)];

        // Obtenez le champion de l'ennemi sélectionné
        $ennemyChampion = $em
            ->getRepository(UserChampion::class)
            ->findOneBy(['user' => $ennemy->getId()]);

        $result = runFight($userChampion, $ennemyChampion, $em);

        return new JsonResponse(['message' => $result]);
    }
    
}
function runFight($user, $ennemy, $em)
{
    $battleLog = [];
    $user_pv = $user->getPv();
    $ennemy_pv = $ennemy->getPv();
    $roundNumber = 0;

    while ($user_pv > 0 && $ennemy_pv > 0) {
        $roundNumber++;
        $attacker = mt_rand(0, 1) == 0 ? $user : $ennemy;
        $defender = $attacker === $user ? $ennemy : $user;

        $damage = mt_rand(10, $attacker->getPower());

        if ($defender === $user) {
            $user_pv -= $damage;
            $defender_pv = $user_pv;
        } else {
            $ennemy_pv -= $damage;
            $defender_pv = $ennemy_pv;
        }

        $battleLog[] = [
            'attacker' => $attacker->getChampion()->getName() . ' de ' . $attacker->getUser()->getUsername(),
            'defender' => $defender->getChampion()->getName() . ' de ' . $defender->getUser()->getUsername(),
            'damage' => $damage,
            'attacker_pv' => $attacker === $user ? $user_pv : $ennemy_pv,
            'defender_pv' => $defender_pv,
        ];

        if ($defender_pv <= 0) {
            break;
        }

        [$user_pv, $ennemy_pv] = [$ennemy_pv, $user_pv];
        [$user, $ennemy] = [$ennemy, $user];
    }

    $winner = $user_pv <= 0 ? $ennemy->getUser()->getUsername() : ($ennemy_pv <= 0 ? $user->getUser()->getUsername() : 'No one');

    $winnerId = $user_pv <= 0 ? $ennemy->getUser() : ($ennemy_pv <= 0 ? $user->getUser() : null);

    $resultRunFight = [
        'battle_log' => $battleLog,
        'winner' => $winner,
        'rounds' => $roundNumber,
    ];

    // Create a new Champion
    $historicFight = new Fight();
    $historicFight->setUser1($user->getUser());
    $historicFight->setUser2($ennemy->getUser());
    $historicFight->setWinner($winnerId);
    $historicFight->setCreatedAt(new \DateTimeImmutable());

    $em->persist($historicFight);
    $em->flush();

    return $resultRunFight;
}
