<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TurnRepository")
 */
class Turn
{
    public const STATUS_IN_PROGRESS = "in-progress";
    public const STATUS_COMPLETED = "turn-completed";
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $start_timestamp;

    /**
     * @ORM\Column(type="guid")
     */
    private $playerId;

    /**
     * @var Player
     * @ORM\ManyToOne(targetEntity="Player", inversedBy="turns")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id")
     */
    private $player;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStartTimestamp(): ?\DateTimeInterface
    {
        return $this->start_timestamp;
    }

    public function setStartTimestamp(\DateTimeInterface $start_timestamp): self
    {
        $this->start_timestamp = $start_timestamp;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlayerId()
    {
        return $this->playerId;
    }

    /**
     * @param mixed $playerId
     * @return Turn
     */
    public function setPlayerId($playerId)
    {
        $this->playerId = $playerId;
        return $this;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * @param Player $player
     * @return Turn
     */
    public function setPlayer(Player $player): self
    {
        $this->player = $player;
        return $this;
    }

}
