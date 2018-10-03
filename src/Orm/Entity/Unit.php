<?php

namespace App\Orm\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UnitRepository")
 */
class Unit
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
    private $attack;

    /**
     * @ORM\Column(type="integer")
     */
    private $defense;

    /**
     * @ORM\Column(type="integer")
     */
    private $health;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $unitType;

    /**
     * @ORM\Column(type="integer")
     */
    private $minRange;

    /**
     * @ORM\Column(type="integer")
     */
    private $maxRange;

    /**
     * @ORM\Column(type="integer")
     */
    private $speed;

    /**
     * @ORM\Column(type="integer")
     */
    private $maxActionPoints;

    /**
     * @ORM\Column(type="integer")
     */
    private $currentActionPoints;

    /**
     * @ORM\Column(type="integer")
     */
    private $actionPointRegenRate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $x_position;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $y_position;

    /**
     * @ORM\Column(type="guid")
     */
    private $playerId;

    /**
     * @var Player
     * @ORM\ManyToOne(targetEntity="Player", inversedBy="units")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id")
     */
    private $player;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAttack(): ?int
    {
        return $this->attack;
    }

    public function setAttack(int $attack): self
    {
        $this->attack = $attack;

        return $this;
    }

    public function getDefense(): ?int
    {
        return $this->defense;
    }

    public function setDefense(int $defense): self
    {
        $this->defense = $defense;

        return $this;
    }

    public function getHealth(): ?int
    {
        return $this->health;
    }

    public function setHealth(int $health): self
    {
        $this->health = $health;

        return $this;
    }

    public function getUnitType(): ?string
    {
        return $this->unitType;
    }

    public function setUnitType(string $unitType): self
    {
        $this->unitType = $unitType;

        return $this;
    }

    public function getMinRange(): ?int
    {
        return $this->minRange;
    }

    public function setMinRange(int $minRange): self
    {
        $this->minRange = $minRange;

        return $this;
    }

    public function getMaxRange(): ?int
    {
        return $this->maxRange;
    }

    public function setMaxRange(int $maxRange): self
    {
        $this->maxRange = $maxRange;

        return $this;
    }

    public function getSpeed(): ?int
    {
        return $this->speed;
    }

    public function setSpeed(int $speed): self
    {
        $this->speed = $speed;

        return $this;
    }

    public function getMaxActionPoints()
    {
        return $this->maxActionPoints;
    }

    public function setMaxActionPoints($maxActionPoints)
    {
        $this->maxActionPoints = $maxActionPoints;
        return $this;
    }

    public function getCurrentActionPoints()
    {
        return $this->currentActionPoints;
    }

    public function setCurrentActionPoints($currentActionPoints)
    {
        $this->currentActionPoints = $currentActionPoints;
        return $this;
    }

    public function getActionPointRegenRate()
    {
        return $this->actionPointRegenRate;
    }

    public function setActionPointRegenRate($actionPointRegenRate)
    {
        $this->actionPointRegenRate = $actionPointRegenRate;
        return $this;
    }

    public function getXPosition(): ?int
    {
        return $this->x_position;
    }

    public function setXPosition(?int $x_position): self
    {
        $this->x_position = $x_position;

        return $this;
    }

    public function getYPosition(): ?int
    {
        return $this->y_position;
    }

    public function setYPosition(?int $y_position): self
    {
        $this->y_position = $y_position;

        return $this;
    }

    public function getPlayerId(): ?string
    {
        return $this->playerId;
    }

    public function setPlayerId(string $playerId): self
    {
        $this->playerId = $playerId;

        return $this;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function setPlayer(Player $player): self
    {
        $this->player = $player;
        return $this;
    }

    public function regenerateActionPoints(): self
    {
        $this->currentActionPoints = min(
            $this->currentActionPoints + $this->getActionPointRegenRate(),
            $this->getMaxActionPoints()
        );

        return $this;
    }

    public function isUnplaced(): bool
    {
        return $this->getXPosition() === null && $this->getYPosition() === null;
    }
}
