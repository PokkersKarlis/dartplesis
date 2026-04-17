<?php

namespace App\Entity;

use App\Repository\ClubRequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClubRequestRepository::class)]
class ClubRequest
{
    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // ── Required contact ─────────────────────────────────────────────────────

    #[ORM\Column(length: 100)]
    private string $name = '';

    #[ORM\Column(length: 100)]
    private string $surname = '';

    #[ORM\Column(length: 180)]
    private string $email = '';

    #[ORM\Column(length: 30)]
    private string $phone = '';

    // ── Profile ───────────────────────────────────────────────────────────────

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $nickname = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateOfBirth = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column]
    private bool $isJunior = false;

    #[ORM\Column(type: Types::JSON)]
    private array $clubRoles = [];

    // ── Darts ────────────────────────────────────────────────────────────────

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $walkOutSong = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $favoriteDouble = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $dartWeight = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $dartModel = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $gripStyle = null;

    #[ORM\Column(nullable: true)]
    private ?int $highestCheckout = null;

    // ── About ────────────────────────────────────────────────────────────────

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $careerHighlight = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $preGameRitual = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $dartsIdol = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $hobbies = null;

    #[ORM\Column(length: 300, nullable: true)]
    private ?string $lifeMotto = null;

    // ── Meta ─────────────────────────────────────────────────────────────────

    #[ORM\Column(length: 45, nullable: true)]
    private ?string $ipAddress = null;

    #[ORM\Column(length: 20)]
    private string $status = self::STATUS_PENDING;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $reviewedAt = null;

    #[ORM\Column(nullable: true)]
    private ?int $approvedPlayerId = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->clubRoles = [];
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = trim($name); return $this; }

    public function getSurname(): string { return $this->surname; }
    public function setSurname(string $surname): self { $this->surname = trim($surname); return $this; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = mb_strtolower(trim($email)); return $this; }

    public function getPhone(): string { return $this->phone; }
    public function setPhone(string $phone): self { $this->phone = trim($phone); return $this; }

    public function getNickname(): ?string { return $this->nickname; }
    public function setNickname(?string $nickname): self { $this->nickname = $nickname; return $this; }

    public function getDateOfBirth(): ?\DateTimeImmutable { return $this->dateOfBirth; }
    public function setDateOfBirth(?\DateTimeImmutable $dob): self { $this->dateOfBirth = $dob; return $this; }

    public function getPhoto(): ?string { return $this->photo; }
    public function setPhoto(?string $photo): self { $this->photo = $photo; return $this; }

    public function isJunior(): bool { return $this->isJunior; }
    public function setIsJunior(bool $v): self { $this->isJunior = $v; return $this; }

    public function getClubRoles(): array { return $this->clubRoles; }
    public function setClubRoles(array $roles): self { $this->clubRoles = array_values(array_unique($roles)); return $this; }

    public function getWalkOutSong(): ?string { return $this->walkOutSong; }
    public function setWalkOutSong(?string $v): self { $this->walkOutSong = $v; return $this; }

    public function getFavoriteDouble(): ?string { return $this->favoriteDouble; }
    public function setFavoriteDouble(?string $v): self { $this->favoriteDouble = $v; return $this; }

    public function getDartWeight(): ?string { return $this->dartWeight; }
    public function setDartWeight(?string $v): self { $this->dartWeight = $v; return $this; }

    public function getDartModel(): ?string { return $this->dartModel; }
    public function setDartModel(?string $v): self { $this->dartModel = $v; return $this; }

    public function getGripStyle(): ?string { return $this->gripStyle; }
    public function setGripStyle(?string $v): self { $this->gripStyle = $v; return $this; }

    public function getHighestCheckout(): ?int { return $this->highestCheckout; }
    public function setHighestCheckout(?int $v): self { $this->highestCheckout = $v; return $this; }

    public function getCareerHighlight(): ?string { return $this->careerHighlight; }
    public function setCareerHighlight(?string $v): self { $this->careerHighlight = $v; return $this; }

    public function getPreGameRitual(): ?string { return $this->preGameRitual; }
    public function setPreGameRitual(?string $v): self { $this->preGameRitual = $v; return $this; }

    public function getDartsIdol(): ?string { return $this->dartsIdol; }
    public function setDartsIdol(?string $v): self { $this->dartsIdol = $v; return $this; }

    public function getHobbies(): ?string { return $this->hobbies; }
    public function setHobbies(?string $v): self { $this->hobbies = $v; return $this; }

    public function getLifeMotto(): ?string { return $this->lifeMotto; }
    public function setLifeMotto(?string $v): self { $this->lifeMotto = $v; return $this; }

    public function getIpAddress(): ?string { return $this->ipAddress; }
    public function setIpAddress(?string $ip): self { $this->ipAddress = $ip; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function getReviewedAt(): ?\DateTimeImmutable { return $this->reviewedAt; }
    public function setReviewedAt(?\DateTimeImmutable $v): self { $this->reviewedAt = $v; return $this; }

    public function getApprovedPlayerId(): ?int { return $this->approvedPlayerId; }
    public function setApprovedPlayerId(?int $v): self { $this->approvedPlayerId = $v; return $this; }

    public function toArray(): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'surname'         => $this->surname,
            'email'           => $this->email,
            'phone'           => $this->phone,
            'nickname'        => $this->nickname,
            'dateOfBirth'     => $this->dateOfBirth?->format('Y-m-d'),
            'photo'           => $this->photo,
            'isJunior'        => $this->isJunior,
            'clubRoles'       => $this->clubRoles,
            'walkOutSong'     => $this->walkOutSong,
            'favoriteDouble'  => $this->favoriteDouble,
            'dartWeight'      => $this->dartWeight,
            'dartModel'       => $this->dartModel,
            'gripStyle'       => $this->gripStyle,
            'highestCheckout' => $this->highestCheckout,
            'careerHighlight' => $this->careerHighlight,
            'preGameRitual'   => $this->preGameRitual,
            'dartsIdol'       => $this->dartsIdol,
            'hobbies'         => $this->hobbies,
            'lifeMotto'       => $this->lifeMotto,
            'status'          => $this->status,
            'createdAt'       => $this->createdAt->format('Y-m-d H:i:s'),
            'reviewedAt'      => $this->reviewedAt?->format('Y-m-d H:i:s'),
            'approvedPlayerId'=> $this->approvedPlayerId,
        ];
    }
}
