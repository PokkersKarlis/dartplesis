<?php

namespace App\Entity;

use App\Repository\BlogPostRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlogPostRepository::class)]
#[ORM\HasLifecycleCallbacks]
class BlogPost
{
    public const STATUS_DRAFT     = 'draft';
    public const STATUS_PUBLISHED = 'published';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $title = '';

    #[ORM\Column(length: 255, unique: true)]
    private string $slug = '';

    #[ORM\Column(type: Types::TEXT)]
    private string $content = '';

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $excerpt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $coverImage = null;

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_DRAFT;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $publishedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $metaTitle = null;

    #[ORM\Column(length: 300, nullable: true)]
    private ?string $metaDescription = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    // ── Getters & Setters ─────────────────────────────────────────────────────

    public function getId(): ?int { return $this->id; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): self { $this->title = trim($title); return $this; }

    public function getSlug(): string { return $this->slug; }
    public function setSlug(string $slug): self { $this->slug = trim($slug); return $this; }

    public function getContent(): string { return $this->content; }
    public function setContent(string $content): self { $this->content = $content; return $this; }

    public function getExcerpt(): ?string { return $this->excerpt; }
    public function setExcerpt(?string $excerpt): self { $this->excerpt = $excerpt; return $this; }

    public function getCoverImage(): ?string { return $this->coverImage; }
    public function setCoverImage(?string $coverImage): self { $this->coverImage = $coverImage; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    public function isPublished(): bool { return $this->status === self::STATUS_PUBLISHED; }

    public function getPublishedAt(): ?\DateTimeImmutable { return $this->publishedAt; }
    public function setPublishedAt(?\DateTimeImmutable $publishedAt): self { $this->publishedAt = $publishedAt; return $this; }

    public function getMetaTitle(): ?string { return $this->metaTitle; }
    public function setMetaTitle(?string $metaTitle): self { $this->metaTitle = $metaTitle; return $this; }

    public function getMetaDescription(): ?string { return $this->metaDescription; }
    public function setMetaDescription(?string $metaDescription): self { $this->metaDescription = $metaDescription; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    // ── Serialization ─────────────────────────────────────────────────────────

    public function toArray(): array
    {
        return [
            'id'              => $this->id,
            'title'           => $this->title,
            'slug'            => $this->slug,
            'content'         => $this->content,
            'excerpt'         => $this->excerpt,
            'coverImage'      => $this->coverImage,
            'status'          => $this->status,
            'publishedAt'     => $this->publishedAt?->format('Y-m-d H:i:s'),
            'metaTitle'       => $this->metaTitle,
            'metaDescription' => $this->metaDescription,
            'createdAt'       => $this->createdAt->format('Y-m-d H:i:s'),
            'updatedAt'       => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }

    /** Omits draft content from public responses. */
    public function toPublicArray(): array
    {
        return [
            'id'              => $this->id,
            'title'           => $this->title,
            'slug'            => $this->slug,
            'content'         => $this->content,
            'excerpt'         => $this->excerpt,
            'coverImage'      => $this->coverImage,
            'publishedAt'     => $this->publishedAt?->format('Y-m-d H:i:s'),
            'metaTitle'       => $this->metaTitle ?? $this->title,
            'metaDescription' => $this->metaDescription ?? $this->excerpt,
        ];
    }
}
