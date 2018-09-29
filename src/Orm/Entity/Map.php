<?php

namespace App\Orm\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MapRepository")
 */
class Map
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $playerCount;

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getPlayerCount()
    {
        return $this->playerCount;
    }

    /**
     * @param mixed $playerCount
     * @return Map
     */
    public function setPlayerCount($playerCount): self
    {
        $this->playerCount = $playerCount;
        return $this;
    }
}
