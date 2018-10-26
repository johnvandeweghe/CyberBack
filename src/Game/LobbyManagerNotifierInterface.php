<?php
namespace App\Game;

use App\Orm\Entity\Game;

interface LobbyManagerNotifierInterface
{
    /**
     * @param Game $game
     * @throws \RuntimeException
     */
    public function notifyLobbyManager(Game $game): void;
}
