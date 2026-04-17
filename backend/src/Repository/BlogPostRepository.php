<?php

namespace App\Repository;

use App\Entity\BlogPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BlogPost>
 */
class BlogPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogPost::class);
    }

    /** Published posts ordered by publishedAt DESC, with optional title search. */
    public function findPublished(int $page = 1, int $perPage = 9, string $search = ''): array
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.status = :status')
            ->setParameter('status', BlogPost::STATUS_PUBLISHED)
            ->orderBy('p.publishedAt', 'DESC')
            ->setFirstResult(($page - 1) * $perPage)
            ->setMaxResults($perPage);

        if ($search !== '') {
            // Search title only — excerpt is LONGTEXT, LIKE on it causes full table scans
            $qb->andWhere('p.title LIKE :q')
               ->setParameter('q', '%' . $search . '%');
        }

        return $qb->getQuery()->getResult();
    }

    public function countPublished(string $search = ''): int
    {
        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.status = :status')
            ->setParameter('status', BlogPost::STATUS_PUBLISHED);

        if ($search !== '') {
            $qb->andWhere('p.title LIKE :q')
               ->setParameter('q', '%' . $search . '%');
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findBySlug(string $slug): ?BlogPost
    {
        return $this->findOneBy(['slug' => $slug]);
    }

    /**
     * Generate a URL-safe slug from a title.
     * Uses a single query (LIKE prefix%) instead of a per-collision loop.
     */
    public function generateUniqueSlug(string $title, ?int $excludeId = null): string
    {
        $base = $this->slugify($title);

        $qb = $this->createQueryBuilder('p')
            ->select('p.slug')
            ->where('p.slug = :base OR p.slug LIKE :prefix')
            ->setParameter('base',   $base)
            ->setParameter('prefix', $base . '-%');

        if ($excludeId !== null) {
            $qb->andWhere('p.id != :id')->setParameter('id', $excludeId);
        }

        $existing = array_column($qb->getQuery()->getArrayResult(), 'slug');

        if (!in_array($base, $existing, true)) {
            return $base;
        }

        $i = 2;
        do {
            $candidate = $base . '-' . $i++;
        } while (in_array($candidate, $existing, true));

        return $candidate;
    }

    private function slugify(string $text): string
    {
        // Transliterate Latvian characters
        $map = [
            'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i',
            'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n', 'š' => 's', 'ū' => 'u',
            'ž' => 'z', 'Ā' => 'a', 'Č' => 'c', 'Ē' => 'e', 'Ģ' => 'g',
            'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'l', 'Ņ' => 'n', 'Š' => 's',
            'Ū' => 'u', 'Ž' => 'z',
        ];

        $text = strtr($text, $map);
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text) ?? $text;
        $text = preg_replace('/[\s-]+/', '-', trim($text)) ?? $text;

        return $text ?: 'post';
    }
}
