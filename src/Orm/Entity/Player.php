<?php

namespace App\Orm\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlayerRepository")
 */
class Player
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
    private $playerNumber;

    /**
     * @ORM\Column(type="guid")
     */
    private $gameId;

    /**
     * @var Game
     * @ORM\ManyToOne(targetEntity="Game", inversedBy="players")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     */
    private $game;

    /**
     * @ORM\OneToMany(targetEntity="Unit", mappedBy="player")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id")
     * @var Unit[]
     */
    private $units;

    /**
     * @ORM\OneToMany(targetEntity="Turn", mappedBy="player")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id")
     * @var Turn[]
     */
    private $turns;

    public function __construct()
    {
        $this->units = new ArrayCollection();
        $this->turns = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPlayerNumber(): ?int
    {
        return $this->playerNumber;
    }

    public function setPlayerNumber(int $playerNumber): self
    {
        $this->playerNumber = $playerNumber;

        return $this;
    }

    public function getGameId(): ?string
    {
        return $this->gameId;
    }

    public function setGameId(string $gameId): self
    {
        $this->gameId = $gameId;

        return $this;
    }

    public function getGame(): Game
    {
        return $this->game;
    }

    public function setGame(Game $game): self
    {
        $this->game = $game;
        return $this;
    }

    /**
     * @return Unit[]
     */
    public function getUnits(): array
    {
        return $this->units->toArray();
    }

    /**
     * @return Turn[]
     */
    public function getTurns(): array
    {
        return $this->turns->toArray();
    }

    public function isTurn(): bool
    {
        return $this->getGame()->getPlayerNumber() === $this->getPlayerNumber();
    }

}
