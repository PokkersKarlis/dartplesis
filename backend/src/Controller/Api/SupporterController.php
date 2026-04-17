<?php

namespace App\Controller\Api;

use App\Entity\Supporter;
use App\Repository\SupporterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;

final class SupporterController extends AbstractController
{
    public function __construct(
        private readonly SupporterRepository $repository,
        private readonly EntityManagerInterface $em,
        private readonly KernelInterface $kernel,
    ) {
    }

    // ── Public endpoint ───────────────────────────────────────────────────────

    #[Route('/api/supporters', name: 'api_public_supporters', methods: ['GET'])]
    public function publicList(): JsonResponse
    {
        $supporters = $this->repository->findBy([], ['sortOrder' => 'ASC', 'createdAt' => 'ASC']);

        return $this->json([
            'supporters' => array_map(fn (Supporter $s) => $s->toArray(), $supporters),
        ]);
    }

    // ── Admin CRUD ────────────────────────────────────────────────────────────

    #[Route('/api/admin/supporters', name: 'api_admin_supporters_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $supporters = $this->repository->findBy([], ['sortOrder' => 'ASC', 'createdAt' => 'ASC']);

        return $this->json([
            'supporters' => array_map(fn (Supporter $s) => $s->toArray(), $supporters),
        ]);
    }

    #[Route('/api/admin/supporters', name: 'api_admin_supporters_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = $this->parseJson($request);
        if ($data === null) {
            return $this->json(['message' => 'Invalid JSON body.'], 400);
        }

        $error = $this->validateName($data);
        if ($error) {
            return $this->json(['message' => $error], 422);
        }

        $supporter = new Supporter();
        $this->hydrate($supporter, $data);
        $this->em->persist($supporter);
        $this->em->flush();

        return $this->json(['supporter' => $supporter->toArray()], 201);
    }

    #[Route('/api/admin/supporters/{id}', name: 'api_admin_supporters_update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $supporter = $this->repository->find($id);
        if (!$supporter) {
            return $this->json(['message' => 'Supporter not found.'], 404);
        }

        $data = $this->parseJson($request);
        if ($data === null) {
            return $this->json(['message' => 'Invalid JSON body.'], 400);
        }

        $error = $this->validateName($data);
        if ($error) {
            return $this->json(['message' => $error], 422);
        }

        $this->hydrate($supporter, $data);
        $this->em->flush();

        return $this->json(['supporter' => $supporter->toArray()]);
    }

    #[Route('/api/admin/supporters/{id}', name: 'api_admin_supporters_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $supporter = $this->repository->find($id);
        if (!$supporter) {
            return $this->json(['message' => 'Supporter not found.'], 404);
        }

        if ($supporter->getLogo()) {
            $old = $this->kernel->getProjectDir() . '/public' . $supporter->getLogo();
            if (file_exists($old)) {
                @unlink($old);
            }
        }

        $this->em->remove($supporter);
        $this->em->flush();

        return $this->json(null, 204);
    }

    #[Route('/api/admin/supporters/{id}/logo', name: 'api_admin_supporters_logo', methods: ['POST'])]
    public function uploadLogo(int $id, Request $request): JsonResponse
    {
        $supporter = $this->repository->find($id);
        if (!$supporter) {
            return $this->json(['message' => 'Supporter not found.'], 404);
        }

        $file = $request->files->get('logo');
        if (!$file) {
            return $this->json(['message' => 'No file uploaded.'], 400);
        }

        $uploadDir = $this->kernel->getProjectDir() . '/public/uploads/supporters';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if ($supporter->getLogo()) {
            $old = $this->kernel->getProjectDir() . '/public' . $supporter->getLogo();
            if (file_exists($old)) {
                @unlink($old);
            }
        }

        $ext      = $file->guessExtension() ?? 'png';
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;

        try {
            $file->move($uploadDir, $filename);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Upload failed: ' . $e->getMessage()], 500);
        }

        $path = '/uploads/supporters/' . $filename;
        $supporter->setLogo($path);
        $this->em->flush();

        return $this->json(['logo' => $path]);
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function parseJson(Request $request): ?array
    {
        if (!str_contains((string) $request->headers->get('Content-Type', ''), 'application/json')) {
            return null;
        }
        $data = json_decode($request->getContent(), true);
        return is_array($data) ? $data : null;
    }

    private function validateName(array $data): ?string
    {
        if (empty(trim((string) ($data['name'] ?? '')))) {
            return 'Name is required.';
        }
        return null;
    }

    private function hydrate(Supporter $supporter, array $data): void
    {
        $supporter->setName(trim((string) ($data['name'] ?? '')));

        $url = $data['url'] ?? null;
        $supporter->setUrl(is_string($url) && trim($url) !== '' ? trim($url) : null);

        $sort = $data['sortOrder'] ?? null;
        $supporter->setSortOrder(is_numeric($sort) ? (int) $sort : 0);
    }
}
