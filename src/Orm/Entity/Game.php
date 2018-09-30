<?php

namespace App\Orm\Entity;

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

    public function getTurnNumber()
    {
        return count($this->getTurns());
    }

    public function getMostRecentTurn(): ?Turn
    {
        return array_reduce($this->getTurns(), function (?Turn $carry, Turn $turn): Turn {
            if($carry === null) {
                return $turn;
            }

            return $turn->getStartTimestamp() > $carry->getStartTimestamp() ? $turn : $carry;
        }, null);
    }

    public function getPlayerNumber()
    {
        $mostRecentTurn = $this->getMostRecentTurn();
        if(!$mostRecentTurn) {
            return 1;
        }
        if($mostRecentTurn->getStatus() === Turn::STATUS_IN_PROGRESS) {
            return $mostRecentTurn->getPlayer()->getPlayerNumber();
        } else {
            return ($mostRecentTurn->getPlayer()->getPlayerNumber() % $this->getMap()->getPlayerCount()) + 1;
        }
    }

    /**
     * @return array
     */
    public function getTurns(): array
    {
        $players = $this->getPlayers();
        $turns = [];
        foreach ($players as $player) {
            $turns = array_merge($turns, $player->getTurns());
        }
        return $turns;
    }

    public function getUnit(int $x, int $y): ?Unit {
        foreach ($this->getPlayers() as $player) {
            foreach ($player->getUnits() as $unit) {
                if ($unit->getXPosition() === $x && $unit->getYPosition() === $y) {
                    return $unit;
                }
            }
        }

        return null;
    }

}
