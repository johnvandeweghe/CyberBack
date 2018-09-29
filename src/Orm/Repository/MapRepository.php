<?php

namespace App\Orm\Repository;

use App\Orm\Entity\Map;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Map|null find($id, $lockMode = null, $lockVersion = null)
 * @method Map|null findOneBy(array $criteria, array $orderBy = null)
 * @method Map[]    findAll()
 * @method Map[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MapRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Map::class);
    }

    public function chooseRandomMap(int $playerCount): ?Map
    {
        return ($maps = $this->findBy(['playerCount' => $playerCount])) ? $maps[array_rand($maps, 1)] : null;
    }
}
