<?php

namespace App\Controller\Api;

use App\Entity\ClubRequest;
use App\Entity\Player;
use App\Repository\ClubRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;

final class ClubRequestController extends AbstractController
{
    // hCaptcha test secret — replace via HCAPTCHA_SECRET env var in production
    private const HCAPTCHA_SECRET_DEFAULT = '0x0000000000000000000000000000000000000000';

    public function __construct(
        private readonly ClubRequestRepository $repo,
        private readonly EntityManagerInterface $em,
        private readonly KernelInterface $kernel,
    ) {
    }

    // Rate limit: max submissions per IP per window
    private const RATE_LIMIT_MAX    = 5;
    private const RATE_LIMIT_WINDOW = '-24 hours';

    // Minimum seconds a real human takes to fill the form
    private const MIN_FILL_SECONDS = 5;

    // ── Public submit ─────────────────────────────────────────────────────────

    #[Route('/api/club-requests', name: 'api_club_request_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        // Accept both multipart/form-data (with photo) and JSON
        $isMultipart = str_contains((string) $request->headers->get('Content-Type', ''), 'multipart');
        $data = $isMultipart
            ? $request->request->all()
            : (json_decode($request->getContent(), true) ?? []);

        // ── Honeypot check ────────────────────────────────────────────────────
        // Bots fill every field; a real browser keeps this off-screen input empty.
        // Return 201 silently so the bot believes it succeeded.
        if (!empty($data['website'] ?? '')) {
            return $this->json(['message' => 'Your application has been received. We will be in touch soon!'], 201);
        }

        // ── Form timing check ─────────────────────────────────────────────────
        // Real humans need at least a few seconds to fill the form.
        $loadedAt = (int) ($data['_t'] ?? 0);
        $elapsed  = time() - (int) ($loadedAt / 1000); // frontend sends ms
        if ($loadedAt > 0 && $elapsed < self::MIN_FILL_SECONDS) {
            return $this->json(['message' => 'Your application has been received. We will be in touch soon!'], 201);
        }

        // ── Captcha verification ──────────────────────────────────────────────
        $token = (string) ($data['captchaToken'] ?? '');
        if (!$this->verifyCaptcha($token)) {
            return $this->json(['message' => 'Captcha verification failed. Please try again.'], 422);
        }

        // ── IP rate limiting ──────────────────────────────────────────────────
        $ip = $request->getClientIp() ?? '0.0.0.0';
        $since = new \DateTimeImmutable(self::RATE_LIMIT_WINDOW);
        if ($this->repo->countByIpSince($ip, $since) >= self::RATE_LIMIT_MAX) {
            return $this->json(['message' => 'Too many submissions. Please try again later.'], 429);
        }

        // ── Validate required fields ──────────────────────────────────────────
        $errors = [];
        if (empty(trim((string) ($data['name'] ?? '')))) $errors[] = 'Name is required.';
        if (empty(trim((string) ($data['surname'] ?? '')))) $errors[] = 'Surname is required.';
        if (empty(trim((string) ($data['email'] ?? '')))) $errors[] = 'Email is required.';
        if (!filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL)) $errors[] = 'Email is invalid.';
        if (empty(trim((string) ($data['phone'] ?? '')))) $errors[] = 'Phone is required.';

        if ($errors) {
            return $this->json(['message' => implode(' ', $errors)], 422);
        }

        $req = new ClubRequest();
        $req->setIpAddress($ip);
        $this->hydrate($req, $data);

        // Handle photo upload
        $photo = $request->files->get('photo');
        if ($photo) {
            $path = $this->savePhoto($photo);
            if ($path) $req->setPhoto($path);
        }

        $this->em->persist($req);
        $this->em->flush();

        return $this->json([
            'message' => 'Your application has been received. We will be in touch soon!',
        ], 201);
    }

    // ── Admin list ────────────────────────────────────────────────────────────

    #[Route('/api/admin/club-requests', name: 'api_admin_club_requests_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $status = $request->query->get('status');
        $criteria = $status ? ['status' => $status] : [];
        $requests = $this->repo->findBy($criteria, ['createdAt' => 'DESC']);

        return $this->json([
            'requests' => array_map(fn(ClubRequest $r) => $r->toArray(), $requests),
            'pendingCount' => $this->repo->countByStatus(ClubRequest::STATUS_PENDING),
        ]);
    }

    // ── Admin approve → creates Player ────────────────────────────────────────

    #[Route('/api/admin/club-requests/{id}/approve', name: 'api_admin_club_requests_approve', methods: ['POST'])]
    public function approve(int $id): JsonResponse
    {
        $req = $this->repo->find($id);
        if (!$req) {
            return $this->json(['message' => 'Request not found.'], 404);
        }
        if ($req->getStatus() === ClubRequest::STATUS_APPROVED) {
            return $this->json(['message' => 'Already approved.'], 409);
        }

        // Transfer to Player
        $player = new Player();
        $player->setName($req->getName());
        $player->setSurname($req->getSurname());
        $player->setNickname($req->getNickname());
        $player->setDateOfBirth($req->getDateOfBirth());
        $player->setPhoto($req->getPhoto());
        $player->setIsJunior($req->isJunior());
        $player->setClubRoles($req->getClubRoles());
        $player->setWalkOutSong($req->getWalkOutSong());
        $player->setFavoriteDouble($req->getFavoriteDouble());
        $player->setDartWeight($req->getDartWeight());
        $player->setDartModel($req->getDartModel());
        $player->setGripStyle($req->getGripStyle());
        $player->setHighestCheckout($req->getHighestCheckout());
        $player->setCareerHighlight($req->getCareerHighlight());
        $player->setPreGameRitual($req->getPreGameRitual());
        $player->setDartsIdol($req->getDartsIdol());
        $player->setHobbies($req->getHobbies());
        $player->setLifeMotto($req->getLifeMotto());

        $this->em->persist($player);
        $this->em->flush(); // assigns $player->getId()

        $req->setStatus(ClubRequest::STATUS_APPROVED);
        $req->setReviewedAt(new \DateTimeImmutable());
        $req->setApprovedPlayerId($player->getId());
        $this->em->flush();

        return $this->json([
            'message' => 'Request approved. Player profile created.',
            'player'  => $player->toArray(),
            'request' => $req->toArray(),
        ]);
    }

    // ── Admin reject ──────────────────────────────────────────────────────────

    #[Route('/api/admin/club-requests/{id}/reject', name: 'api_admin_club_requests_reject', methods: ['POST'])]
    public function reject(int $id): JsonResponse
    {
        $req = $this->repo->find($id);
        if (!$req) {
            return $this->json(['message' => 'Request not found.'], 404);
        }

        $req->setStatus(ClubRequest::STATUS_REJECTED);
        $req->setReviewedAt(new \DateTimeImmutable());
        $this->em->flush();

        return $this->json(['request' => $req->toArray()]);
    }

    // ── Admin delete ──────────────────────────────────────────────────────────

    #[Route('/api/admin/club-requests/{id}', name: 'api_admin_club_requests_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $req = $this->repo->find($id);
        if (!$req) {
            return $this->json(['message' => 'Request not found.'], 404);
        }

        $this->em->remove($req);
        $this->em->flush();

        return $this->json(null, 204);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function hydrate(ClubRequest $req, array $data): void
    {
        $s = fn(mixed $v): ?string => (is_string($v) && trim($v) !== '') ? trim($v) : null;

        $req->setName(trim((string) ($data['name'] ?? '')));
        $req->setSurname(trim((string) ($data['surname'] ?? '')));
        $req->setEmail((string) ($data['email'] ?? ''));
        $req->setPhone(trim((string) ($data['phone'] ?? '')));
        $req->setNickname($s($data['nickname'] ?? null));
        $req->setIsJunior((bool) ($data['isJunior'] ?? false));
        $req->setWalkOutSong($s($data['walkOutSong'] ?? null));
        $req->setFavoriteDouble($s($data['favoriteDouble'] ?? null));
        $req->setDartWeight($s($data['dartWeight'] ?? null));
        $req->setDartModel($s($data['dartModel'] ?? null));
        $req->setGripStyle($s($data['gripStyle'] ?? null));
        $req->setCareerHighlight($s($data['careerHighlight'] ?? null));
        $req->setPreGameRitual($s($data['preGameRitual'] ?? null));
        $req->setDartsIdol($s($data['dartsIdol'] ?? null));
        $req->setHobbies($s($data['hobbies'] ?? null));
        $req->setLifeMotto($s($data['lifeMotto'] ?? null));

        // Roles: may come as JSON string (multipart) or array (JSON body)
        $roles = $data['clubRoles'] ?? [];
        if (is_string($roles)) {
            $roles = json_decode($roles, true) ?? [];
        }
        $req->setClubRoles(is_array($roles) ? $roles : []);

        // Date of birth
        $dob = $data['dateOfBirth'] ?? null;
        if ($dob && is_string($dob)) {
            try {
                $req->setDateOfBirth(new \DateTimeImmutable($dob));
            } catch (\Exception) {}
        }

        // Highest checkout
        $co = $data['highestCheckout'] ?? null;
        $req->setHighestCheckout(($co !== null && $co !== '') ? (int) $co : null);
    }

    private function savePhoto(mixed $file): ?string
    {
        $uploadDir = $this->kernel->getProjectDir() . '/public/uploads/players';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext      = $file->guessExtension() ?? 'jpg';
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;

        try {
            $file->move($uploadDir, $filename);
            return '/uploads/players/' . $filename;
        } catch (\Exception) {
            return null;
        }
    }

    private function verifyCaptcha(string $token): bool
    {
        if (empty($token)) {
            return false;
        }

        $secret = $_ENV['HCAPTCHA_SECRET'] ?? $_SERVER['HCAPTCHA_SECRET'] ?? getenv('HCAPTCHA_SECRET') ?: self::HCAPTCHA_SECRET_DEFAULT;

        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query(['secret' => $secret, 'response' => $token]),
                'timeout' => 5,
            ],
        ]);

        $result = @file_get_contents('https://hcaptcha.com/siteverify', false, $context);
        if ($result === false) {
            return false;
        }

        $data = json_decode($result, true);
        return (bool) ($data['success'] ?? false);
    }
}
