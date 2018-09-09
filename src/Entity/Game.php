<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
 */
class Game
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\Column(type="guid")
     */
    private $mapId;

    /**
     * @var Map
     * @ORM\ManyToOne(targetEntity="Map")
     * @ORM\JoinColumn(name="map_id", referencedColumnName="id")
     */
    private $map;

    /**
     * @ORM\OneToMany(targetEntity="Player", mappedBy="game")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     * @var Player[]
     */
    private $players;

    public function __construct(Map $map) {
        $this->map = $map;
        $this->players = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getMapId()
    {
        return $this->mapId;
    }

    /**
     * @return Map
     */
    public function getMap(): Map
    {
        return $this->map;
    }

    /**
     * @return Player[]
     */
    public function getPlayers()
    {
        return $this->players;
    }

}
