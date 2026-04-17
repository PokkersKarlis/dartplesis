<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player
{
    public const GRIP_STYLES = ['Front', 'Middle', 'Rear'];

    public const FAVORITE_DOUBLES = [
        'D1','D2','D3','D4','D5','D6','D7','D8','D9','D10',
        'D11','D12','D13','D14','D15','D16','D17','D18','D19','D20',
        'Bull',
    ];

    public const CLUB_ROLES = [
        'player', 'master_player', 'trainer', 'master_yoda',
        'club_manager', 'club_captain', 'vice_captain',
        'committee_member', 'social_media',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private string $name = '';

    #[ORM\Column(length: 100)]
    private string $surname = '';

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $nickname = null;

    // ── New fields ───────────────────────────────────────────────────────────

    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $dateOfBirth = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column]
    private bool $isJunior = false;

    #[ORM\Column(type: Types::JSON)]
    private array $clubRoles = [];

    // ── Darts specifics ──────────────────────────────────────────────────────

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

    // ── Stats ────────────────────────────────────────────────────────────────

    #[ORM\Column(nullable: true)]
    private ?int $highestCheckout = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $careerHighlight = null;

    // ── Fun stuff ────────────────────────────────────────────────────────────

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $preGameRitual = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $dartsIdol = null;

    #[ORM\Column(length: 200, nullable: true)]
    private ?string $hobbies = null;

    #[ORM\Column(length: 300, nullable: true)]
    private ?string $lifeMotto = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt  = new \DateTimeImmutable();
        $this->clubRoles  = [];
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = trim($name); return $this; }

    public function getSurname(): string { return $this->surname; }
    public function setSurname(string $surname): self { $this->surname = trim($surname); return $this; }

    public function getNickname(): ?string { return $this->nickname; }
    public function setNickname(?string $nickname): self { $this->nickname = $nickname !== null ? trim($nickname) : null; return $this; }

    public function getDateOfBirth(): ?\DateTimeImmutable { return $this->dateOfBirth; }
    public function setDateOfBirth(?\DateTimeImmutable $dateOfBirth): self { $this->dateOfBirth = $dateOfBirth; return $this; }

    public function getPhoto(): ?string { return $this->photo; }
    public function setPhoto(?string $photo): self { $this->photo = $photo; return $this; }

    public function isJunior(): bool { return $this->isJunior; }
    public function setIsJunior(bool $isJunior): self { $this->isJunior = $isJunior; return $this; }

    public function getClubRoles(): array { return $this->clubRoles ?? []; }
    public function setClubRoles(array $clubRoles): self { $this->clubRoles = array_values(array_unique($clubRoles)); return $this; }

    public function getWalkOutSong(): ?string { return $this->walkOutSong; }
    public function setWalkOutSong(?string $walkOutSong): self { $this->walkOutSong = $walkOutSong; return $this; }

    public function getFavoriteDouble(): ?string { return $this->favoriteDouble; }
    public function setFavoriteDouble(?string $favoriteDouble): self { $this->favoriteDouble = $favoriteDouble; return $this; }

    public function getDartWeight(): ?string { return $this->dartWeight; }
    public function setDartWeight(?string $dartWeight): self { $this->dartWeight = $dartWeight; return $this; }

    public function getDartModel(): ?string { return $this->dartModel; }
    public function setDartModel(?string $dartModel): self { $this->dartModel = $dartModel; return $this; }

    public function getGripStyle(): ?string { return $this->gripStyle; }
    public function setGripStyle(?string $gripStyle): self { $this->gripStyle = $gripStyle; return $this; }

    public function getHighestCheckout(): ?int { return $this->highestCheckout; }
    public function setHighestCheckout(?int $highestCheckout): self { $this->highestCheckout = $highestCheckout; return $this; }

    public function getCareerHighlight(): ?string { return $this->careerHighlight; }
    public function setCareerHighlight(?string $careerHighlight): self { $this->careerHighlight = $careerHighlight; return $this; }

    public function getPreGameRitual(): ?string { return $this->preGameRitual; }
    public function setPreGameRitual(?string $preGameRitual): self { $this->preGameRitual = $preGameRitual; return $this; }

    public function getDartsIdol(): ?string { return $this->dartsIdol; }
    public function setDartsIdol(?string $dartsIdol): self { $this->dartsIdol = $dartsIdol; return $this; }

    public function getHobbies(): ?string { return $this->hobbies; }
    public function setHobbies(?string $hobbies): self { $this->hobbies = $hobbies; return $this; }

    public function getLifeMotto(): ?string { return $this->lifeMotto; }
    public function setLifeMotto(?string $lifeMotto): self { $this->lifeMotto = $lifeMotto; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    public function toArray(): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'surname'          => $this->surname,
            'nickname'         => $this->nickname,
            'dateOfBirth'      => $this->dateOfBirth?->format('Y-m-d'),
            'photo'            => $this->photo,
            'isJunior'         => $this->isJunior,
            'clubRoles'        => $this->clubRoles ?? [],
            'walkOutSong'      => $this->walkOutSong,
            'favoriteDouble'   => $this->favoriteDouble,
            'dartWeight'       => $this->dartWeight,
            'dartModel'        => $this->dartModel,
            'gripStyle'        => $this->gripStyle,
            'highestCheckout'  => $this->highestCheckout,
            'careerHighlight'  => $this->careerHighlight,
            'preGameRitual'    => $this->preGameRitual,
            'dartsIdol'        => $this->dartsIdol,
            'hobbies'          => $this->hobbies,
            'lifeMotto'        => $this->lifeMotto,
            'createdAt'        => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }
}
