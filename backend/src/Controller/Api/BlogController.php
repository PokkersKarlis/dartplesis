<?php

namespace App\Controller\Api;

use App\Entity\BlogPost;
use App\Repository\BlogPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;

final class BlogController extends AbstractController
{
    public function __construct(
        private readonly BlogPostRepository $repo,
        private readonly EntityManagerInterface $em,
        private readonly KernelInterface $kernel,
    ) {
    }

    // ── Public endpoints ──────────────────────────────────────────────────────

    #[Route('/api/blog', name: 'api_blog_list', methods: ['GET'])]
    public function publicList(Request $request): JsonResponse
    {
        $page    = max(1, (int) $request->query->get('page', 1));
        $perPage = 9;
        $search  = trim((string) $request->query->get('q', ''));

        $posts = $this->repo->findPublished($page, $perPage, $search);
        $total = $this->repo->countPublished($search);

        return $this->json([
            'posts'      => array_map(fn(BlogPost $p) => $p->toPublicArray(), $posts),
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'totalPages' => (int) ceil($total / $perPage),
        ]);
    }

    #[Route('/api/blog/{slug}', name: 'api_blog_show', methods: ['GET'])]
    public function publicShow(string $slug): JsonResponse
    {
        $post = $this->repo->findBySlug($slug);
        if (!$post || !$post->isPublished()) {
            return $this->json(['message' => 'Post not found.'], 404);
        }

        return $this->json(['post' => $post->toPublicArray()]);
    }

    // ── Admin endpoints ───────────────────────────────────────────────────────

    #[Route('/api/admin/blog', name: 'api_admin_blog_list', methods: ['GET'])]
    public function adminList(Request $request): JsonResponse
    {
        $page    = max(1, (int) $request->query->get('page', 1));
        $perPage = 15;
        $search  = trim((string) $request->query->get('q', ''));
        $status  = $request->query->get('status', '');

        $qb = $this->repo->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        $countQb = $this->repo->createQueryBuilder('p')->select('COUNT(p.id)');

        if ($search !== '') {
            $qb->andWhere('p.title LIKE :q')->setParameter('q', '%' . $search . '%');
            $countQb->andWhere('p.title LIKE :q')->setParameter('q', '%' . $search . '%');
        }

        if ($status !== '') {
            $qb->andWhere('p.status = :status')->setParameter('status', $status);
            $countQb->andWhere('p.status = :status')->setParameter('status', $status);
        }

        $posts = $qb->getQuery()->getResult();
        $total = (int) $countQb->getQuery()->getSingleScalarResult();

        return $this->json([
            'posts'      => array_map(fn(BlogPost $p) => $p->toArray(), $posts),
            'total'      => $total,
            'page'       => $page,
            'perPage'    => $perPage,
            'totalPages' => (int) ceil($total / $perPage),
        ]);
    }

    #[Route('/api/admin/blog', name: 'api_admin_blog_create', methods: ['POST'])]
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

        $post = new BlogPost();
        $this->hydrate($post, $data);

        if ($post->getSlug() === '') {
            $post->setSlug($this->repo->generateUniqueSlug($post->getTitle()));
        } else {
            $post->setSlug($this->repo->generateUniqueSlug($post->getSlug()));
        }

        $this->em->persist($post);
        $this->em->flush();

        return $this->json(['post' => $post->toArray()], 201);
    }

    #[Route('/api/admin/blog/{id}', name: 'api_admin_blog_show', methods: ['GET'])]
    public function adminShow(int $id): JsonResponse
    {
        $post = $this->repo->find($id);
        if (!$post) {
            return $this->json(['message' => 'Post not found.'], 404);
        }

        return $this->json(['post' => $post->toArray()]);
    }

    #[Route('/api/admin/blog/{id}', name: 'api_admin_blog_update', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $post = $this->repo->find($id);
        if (!$post) {
            return $this->json(['message' => 'Post not found.'], 404);
        }

        $data = $this->parseJson($request);
        if ($data === null) {
            return $this->json(['message' => 'Invalid JSON body.'], 400);
        }

        $errors = $this->validate($data);
        if ($errors) {
            return $this->json(['message' => implode(' ', $errors)], 422);
        }

        $this->hydrate($post, $data, $id);
        $this->em->flush();

        return $this->json(['post' => $post->toArray()]);
    }

    #[Route('/api/admin/blog/{id}', name: 'api_admin_blog_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $post = $this->repo->find($id);
        if (!$post) {
            return $this->json(['message' => 'Post not found.'], 404);
        }

        // Remove cover image file if present
        if ($post->getCoverImage()) {
            $path = $this->kernel->getProjectDir() . '/public' . $post->getCoverImage();
            if (file_exists($path)) {
                @unlink($path);
            }
        }

        $this->em->remove($post);
        $this->em->flush();

        return $this->json(null, 204);
    }

    #[Route('/api/admin/blog/{id}/cover', name: 'api_admin_blog_cover', methods: ['POST'])]
    public function uploadCover(int $id, Request $request): JsonResponse
    {
        $post = $this->repo->find($id);
        if (!$post) {
            return $this->json(['message' => 'Post not found.'], 404);
        }

        $file = $request->files->get('cover');
        if (!$file) {
            return $this->json(['message' => 'No file uploaded.'], 400);
        }

        $uploadDir = $this->kernel->getProjectDir() . '/public/uploads/blog';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Remove old cover
        if ($post->getCoverImage()) {
            $old = $this->kernel->getProjectDir() . '/public' . $post->getCoverImage();
            if (file_exists($old)) {
                @unlink($old);
            }
        }

        $ext      = $file->guessExtension() ?? 'jpg';
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;

        try {
            $file->move($uploadDir, $filename);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Upload failed: ' . $e->getMessage()], 500);
        }

        $path = '/uploads/blog/' . $filename;
        $post->setCoverImage($path);
        $this->em->flush();

        return $this->json(['coverImage' => $path]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

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

        if (empty(trim((string) ($data['title'] ?? '')))) {
            $errors[] = 'Title is required.';
        }

        $status = $data['status'] ?? BlogPost::STATUS_DRAFT;
        if (!in_array($status, [BlogPost::STATUS_DRAFT, BlogPost::STATUS_PUBLISHED], true)) {
            $errors[] = 'Invalid status.';
        }

        return $errors;
    }

    private function hydrate(BlogPost $post, array $data, ?int $excludeId = null): void
    {
        $s = fn(mixed $v): ?string => (is_string($v) && trim($v) !== '') ? trim($v) : null;

        $post->setTitle(trim((string) ($data['title'] ?? '')));
        $post->setContent((string) ($data['content'] ?? ''));
        $post->setExcerpt($s($data['excerpt'] ?? null));
        $post->setMetaTitle($s($data['metaTitle'] ?? null));
        $post->setMetaDescription($s($data['metaDescription'] ?? null));

        $status = $data['status'] ?? BlogPost::STATUS_DRAFT;
        $wasPublished = $post->isPublished();
        $post->setStatus($status);

        // Auto-set publishedAt when transitioning to published
        if ($status === BlogPost::STATUS_PUBLISHED && !$wasPublished && $post->getPublishedAt() === null) {
            $post->setPublishedAt(new \DateTimeImmutable());
        } elseif ($status === BlogPost::STATUS_DRAFT) {
            $post->setPublishedAt(null);
        }

        // Explicit publishedAt override
        $publishedAt = $data['publishedAt'] ?? null;
        if ($publishedAt && is_string($publishedAt)) {
            try {
                $post->setPublishedAt(new \DateTimeImmutable($publishedAt));
            } catch (\Exception) {
                // keep auto-set value
            }
        }

        // Slug: only update on explicit slug provided or when creating
        $slugInput = trim((string) ($data['slug'] ?? ''));
        if ($slugInput !== '') {
            $slug = $this->repo->generateUniqueSlug($slugInput, $excludeId);
            $post->setSlug($slug);
        }
    }
}
