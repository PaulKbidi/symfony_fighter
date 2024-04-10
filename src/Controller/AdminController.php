<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Champion;


#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AdminController.php',
        ]);
    }
    
    #[Route('/create_champion', name: 'app_admin_create_champion', methods: ['POST'])]
    public function createChampion(Request $request, EntityManagerInterface $em): JsonResponse
    {

        $data = json_decode($request->getContent(), true);
        $name = $data['name'];
        $pv = $data['pv'];
        $power = $data['power'];

        $champion = new Champion();
        $champion->setName($name);
        $champion->setPv($pv);
        $champion->setPower($power);

        $em ->persist($champion);

        $em->flush();

        return new JsonResponse(['message' => 'Champion created']);
    }
}
