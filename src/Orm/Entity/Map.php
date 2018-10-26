<?php

namespace App\Orm\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Orm\Repository\MapRepository")
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

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $lobbyManagerName;

    /**
     * @var LobbyManager
     * @ORM\ManyToOne(targetEntity="LobbyManager")
     * @ORM\JoinColumn(name="lobby_manager_name", referencedColumnName="name")
     */
    private $lobbyManager;

    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPlayerCount()
    {
        return $this->playerCount;
    }

    /**
     * @param int $playerCount
     * @return Map
     */
    public function setPlayerCount($playerCount): self
    {
        $this->playerCount = $playerCount;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getLobbyManagerName(): ?string
    {
        return $this->lobbyManagerName;
    }

    /**
     * @param null|string $lobbyManagerName
     * @return Map
     */
    public function setLobbyManagerName(?string $lobbyManagerName)
    {
        $this->lobbyManagerName = $lobbyManagerName;
        return $this;
    }

    /**
     * @return null|LobbyManager
     */
    public function getLobbyManager(): ?LobbyManager
    {
        return $this->lobbyManager;
    }

    /**
     * @param null|LobbyManager $lobbyManager
     * @return Map
     */
    public function setLobbyManager(?LobbyManager $lobbyManager)
    {
        $this->lobbyManager = $lobbyManager;
        return $this;
    }


}
