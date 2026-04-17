<?php

namespace App\Controller\Api;

use App\Entity\Player;
use App\Repository\PlayerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class PublicPlayerController extends AbstractController
{
    public function __construct(
        private readonly PlayerRepository $playerRepository,
    ) {
    }

    #[Route('/api/players', name: 'api_public_players', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $players = $this->playerRepository->findAllSorted();

        return $this->json([
            'players' => array_map(fn (Player $p) => $p->toArray(), $players),
        ]);
    }
}

