<?php
namespace App\Game;

use App\Game\Exception\GameFullException;
use App\Game\Exception\InsufficientActionPointsException;
use App\Game\Exception\InvalidPathException;
use App\Game\Exception\OutOfTurnException;
use App\Game\Exception\UnableToJoinGameException;
use App\Game\Exception\UnplacedUnitsException;
use App\Orm\Entity\Game;
use App\Orm\Entity\Player;
use App\Orm\Entity\Turn;
use App\Orm\Entity\Unit;

interface ManagerInterface
{
    /**
     * @param int $numberOfPlayers
     * @return Game
     */
    public function startGame(int $numberOfPlayers): Game;

    /**
     * @param string $gameId
     * @return null|Game
     */
    public function getGame(string $gameId): ?Game;

    /**
     * @param Game $game
     * @return Player
     * @throws UnableToJoinGameException
     * @throws GameFullException
     */
    public function joinGame(Game $game): Player;

    /**
     * @param string $playerId
     * @return Player|null
     */
    public function getPlayer(string $playerId): ?Player;

    /**
     * @param Player $player
     * @return Turn
     * @throws OutOfTurnException
     */
    public function startTurn(Player $player): Turn;

    /**
     * @param Turn $turn
     * @throws OutOfTurnException
     * @throws UnplacedUnitsException
     */
    public function endTurn(Turn $turn): void;

    /**
     * @param string $turnId
     * @return Turn|null
     */
    public function getTurn(string $turnId): ?Turn;

    /**
     * @param Game $game
     * @return Unit[]
     */
    public function getUnits(Game $game): array;

    /**
     * @param string $unitId
     * @return Unit|null
     */
    public function getUnit(string $unitId): ?Unit;

    /**
     * @param Unit $unit
     * @param array $path
     * @throws OutOfTurnException
     * @throws InsufficientActionPointsException
     * @throws InvalidPathException
     */
    public function moveUnit(Unit $unit, array $path): void;



}
