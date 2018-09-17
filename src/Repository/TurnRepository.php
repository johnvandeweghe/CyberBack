<?php

namespace App\Repository;

use App\Entity\Player;
use App\Entity\Turn;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Turn|null find($id, $lockMode = null, $lockVersion = null)
 * @method Turn|null findOneBy(array $criteria, array $orderBy = null)
 * @method Turn[]    findAll()
 * @method Turn[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TurnRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Turn::class);
    }

    public function startTurn(Player $player, ?\DateTimeInterface $now = null): Turn
    {
        $turn = new Turn();
        $turn->setPlayer($player);
        $turn->setStartTimestamp($now !== null ?: new \DateTime());
        $turn->setStatus(Turn::STATUS_IN_PROGRESS);
        return $turn;
    }

}
