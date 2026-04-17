<?php

namespace App\Controller\Api;

use App\Entity\Player;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/admin/players', name: 'api_admin_players_')]
final class PlayerController extends AbstractController
{
    public function __construct(
        private readonly PlayerRepository $playerRepository,
        private readonly EntityManagerInterface $em,
        private readonly KernelInterface $kernel,
    ) {
    }

    // ── Admin CRUD ────────────────────────────────────────────────────────────

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $players = $this->playerRepository->findAllSorted();

        return $this->json([
            'players' => array_map(fn(Player $p) => $p->toArray(), $players),
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = $this->parseJson($request);
        if ($data === null) {
            return $this->json(['message' => 'Invalid JSON body.'], 400);
        }

        $errors = $this->validate($data);
        if ($errors) {
            return $this->json(['message' => implode(' ', $errors)], 422);
        }

        $player = new Player();
        $this->hydrate($player, $data);
        $this->em->persist($player);
        $this->em->flush();

        return $this->json(['player' => $player->toArray()], 201);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $player = $this->playerRepository->find($id);
        if (!$player) {
            return $this->json(['message' => 'Player not found.'], 404);
        }

        return $this->json(['player' => $player->toArray()]);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $player = $this->playerRepository->find($id);
        if (!$player) {
            return $this->json(['message' => 'Player not found.'], 404);
        }

        $data = $this->parseJson($request);
        if ($data === null) {
            return $this->json(['message' => 'Invalid JSON body.'], 400);
        }

        $errors = $this->validate($data);
        if ($errors) {
            return $this->json(['message' => implode(' ', $errors)], 422);
        }

        $this->hydrate($player, $data);
        $this->em->flush();

        return $this->json(['player' => $player->toArray()]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $player = $this->playerRepository->find($id);
        if (!$player) {
            return $this->json(['message' => 'Player not found.'], 404);
        }

        $this->em->remove($player);
        $this->em->flush();

        return $this->json(null, 204);
    }

    #[Route('/{id}/photo', name: 'photo', methods: ['POST'])]
    public function uploadPhoto(int $id, Request $request): JsonResponse
    {
        $player = $this->playerRepository->find($id);
        if (!$player) {
            return $this->json(['message' => 'Player not found.'], 404);
        }

        $file = $request->files->get('photo');
        if (!$file) {
            return $this->json(['message' => 'No file uploaded.'], 400);
        }

        $uploadDir = $this->kernel->getProjectDir() . '/public/uploads/players';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Remove old photo
        if ($player->getPhoto()) {
            $old = $this->kernel->getProjectDir() . '/public' . $player->getPhoto();
            if (file_exists($old)) @unlink($old);
        }

        $ext      = $file->guessExtension() ?? 'jpg';
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;

        try {
            $file->move($uploadDir, $filename);
        } catch (\Exception $e) {
            return $this->json(['message' => 'File upload failed: ' . $e->getMessage()], 500);
        }

        $path = '/uploads/players/' . $filename;
        $player->setPhoto($path);
        $this->em->flush();

        return $this->json(['photo' => $path]);
    }

    private function parseJson(Request $request): ?array
    {
        if (!str_contains((string) $request->headers->get('Content-Type', ''), 'application/json')) {
            return null;
        }
        $data = json_decode($request->getContent(), true);
        return is_array($data) ? $data : null;
    }

    private function validate(array $data): array
    {
        $errors = [];

        if (empty(trim((string) ($data['name'] ?? '')))) {
            $errors[] = 'Name is required.';
        }
        if (empty(trim((string) ($data['surname'] ?? '')))) {
            $errors[] = 'Surname is required.';
        }

        $checkout = $data['highestCheckout'] ?? null;
        if ($checkout !== null && $checkout !== '') {
            $val = (int) $checkout;
            if ($val < 2 || $val > 170) {
                $errors[] = 'Highest checkout must be between 2 and 170.';
            }
        }

        if (!empty($data['favoriteDouble']) && !in_array($data['favoriteDouble'], Player::FAVORITE_DOUBLES, true)) {
            $errors[] = 'Invalid favorite double value.';
        }

        if (!empty($data['gripStyle']) && !in_array($data['gripStyle'], Player::GRIP_STYLES, true)) {
            $errors[] = 'Invalid grip style value.';
        }

        return $errors;
    }

    private function hydrate(Player $player, array $data): void
    {
        $s = fn(mixed $v): ?string => (is_string($v) && trim($v) !== '') ? trim($v) : null;

        $player->setName(trim((string) ($data['name'] ?? '')));
        $player->setSurname(trim((string) ($data['surname'] ?? '')));
        $player->setNickname($s($data['nickname'] ?? null));
        $player->setIsJunior((bool) ($data['isJunior'] ?? false));
        $player->setWalkOutSong($s($data['walkOutSong'] ?? null));
        $player->setFavoriteDouble($s($data['favoriteDouble'] ?? null));
        $player->setDartWeight($s($data['dartWeight'] ?? null));
        $player->setDartModel($s($data['dartModel'] ?? null));
        $player->setGripStyle($s($data['gripStyle'] ?? null));
        $player->setCareerHighlight($s($data['careerHighlight'] ?? null));
        $player->setPreGameRitual($s($data['preGameRitual'] ?? null));
        $player->setDartsIdol($s($data['dartsIdol'] ?? null));
        $player->setHobbies($s($data['hobbies'] ?? null));
        $player->setLifeMotto($s($data['lifeMotto'] ?? null));

        // Club roles
        $roles = $data['clubRoles'] ?? [];
        $player->setClubRoles(is_array($roles) ? $roles : []);

        // Date of birth
        $dob = $data['dateOfBirth'] ?? null;
        if ($dob && is_string($dob)) {
            try {
                $player->setDateOfBirth(new \DateTimeImmutable($dob));
            } catch (\Exception) {
                $player->setDateOfBirth(null);
            }
        } else {
            $player->setDateOfBirth(null);
        }

        $checkout = $data['highestCheckout'] ?? null;
        $player->setHighestCheckout(($checkout !== null && $checkout !== '') ? (int) $checkout : null);
    }
}
