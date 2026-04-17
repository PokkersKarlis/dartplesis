<?php

namespace App\Repository;

use App\Entity\Player;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Player>
 */
class PlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
    }

    /**
     * All players sorted seniors-first then by surname/name.
     * Single query — no post-processing sort needed in PHP.
     *
     * @return Player[]
     */
    public function findAllSorted(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.isJunior', 'ASC')   // seniors (false=0) before juniors (true=1)
            ->addOrderBy('p.surname', 'ASC')
            ->addOrderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
