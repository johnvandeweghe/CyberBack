<?php
namespace App\Game;

use App\Game\Exception\GameFullException;
use App\Game\Exception\InsufficientActionPointsException;
use App\Game\Exception\InvalidPathException;
use App\Game\Exception\InvalidTargetException;
use App\Game\Exception\OutOfTurnException;
use App\Game\Exception\UnableToJoinGameException;
use App\Game\Exception\UnplacedUnitsException;
use App\Orm\Entity\Game;
use App\Orm\Entity\Map;
use App\Orm\Entity\Player;
use App\Orm\Entity\Turn;
use App\Orm\Entity\Unit;

interface ManagerInterface
{
    /**
     * @return Map[]
     */
    public function getMaps(): array;

    /**
     * @param string $mapId
     * @return null|Map
     */
    public function getMap(string $mapId): ?Map;

    /**
     * @param Map $map
     * @return Game
     */
    public function startGame(Map $map): Game;

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
     * @param Turn $turn
     * @param Unit $unit
     * @param array $path
     * @throws OutOfTurnException
     * @throws InsufficientActionPointsException
     * @throws InvalidPathException
     */
    public function moveUnit(Turn $turn, Unit $unit, array $path): void;


    /**
     * @param Turn $turn
     * @param Unit $unit
     * @param Unit $targetUnit
     * @throws OutOfTurnException
     * @throws InsufficientActionPointsException
     * @throws InvalidTargetException
     */
    public function attackUnit(Turn $turn, Unit $unit, Unit $targetUnit): void;

}
