<?php
namespace App\Game;

use App\Game\Exception\GameFullException;
use App\Game\Exception\InsufficientActionPointsException;
use App\Game\Exception\InvalidPathException;
use App\Game\Exception\OutOfTurnException;
use App\Game\Exception\UnableToJoinGameException;
use App\Game\Exception\UnplacedUnitsException;
use App\Game\MapData\MapDataRetriever;
use App\Game\MapData\Tile;
use App\Game\MapData\UnitInitializer;
use App\Orm\Entity\Game;
use App\Orm\Entity\Player;
use App\Orm\Entity\Turn;
use App\Orm\Entity\Unit;
use App\Orm\Repository\GameRepository;
use App\Orm\Repository\MapRepository;
use App\Orm\Repository\PlayerRepository;
use App\Orm\Repository\TurnRepository;
use App\Orm\Repository\UnitRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Psr\Log\LoggerInterface;
use Pusher\Pusher;
use Pusher\PusherException;

class Manager implements ManagerInterface
{
    public const UNIT_ACTION_MOVE = 'move';
    public const UNIT_ACTION_ATTACK = 'attack';
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var MapRepository
     */
    private $mapRepository;
    /**
     * @var GameRepository
     */
    private $gameRepository;
    /**
     * @var TurnRepository
     */
    private $turnRepository;
    /**
     * @var PlayerRepository
     */
    private $playerRepository;
    /**
     * @var Pusher
     */
    private $pusher;
    /**
     * @var MapDataRetriever
     */
    private $mapDataRetriever;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var UnitRepository
     */
    private $unitRepository;

    /**
     * Manager constructor.
     * @param EntityManager $entityManager
     * @param MapRepository $mapRepository
     * @param GameRepository $gameRepository
     * @param TurnRepository $turnRepository
     * @param PlayerRepository $playerRepository
     * @param UnitRepository $unitRepository
     * @param Pusher $pusher
     * @param MapDataRetriever $mapDataRetriever
     * @param LoggerInterface $logger
     */
    public function __construct(
        EntityManager $entityManager,
        MapRepository $mapRepository,
        GameRepository $gameRepository,
        TurnRepository $turnRepository,
        PlayerRepository $playerRepository,
        UnitRepository $unitRepository,
        Pusher $pusher,
        MapDataRetriever $mapDataRetriever,
        LoggerInterface $logger
    )
    {
        $this->entityManager = $entityManager;
        $this->mapRepository = $mapRepository;
        $this->gameRepository = $gameRepository;
        $this->turnRepository = $turnRepository;
        $this->playerRepository = $playerRepository;
        $this->pusher = $pusher;
        $this->mapDataRetriever = $mapDataRetriever;
        $this->logger = $logger;
        $this->unitRepository = $unitRepository;
    }

    /**
     * @param int $numberOfPlayers
     * @return Game
     */
    public function startGame(int $numberOfPlayers): Game
    {
        $map = $this->mapRepository->chooseRandomMap(2);
        $game = new Game($map);

        try {
            $this->entityManager->persist($game);
            $this->entityManager->flush();

            return $game;
        } catch (ORMException $e) {
            throw new \RuntimeException("Unable to communicate with DB: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string $gameId
     * @return null|Game
     */
    public function getGame(string $gameId): ?Game
    {
        return $this->gameRepository->find($gameId);
    }

    /**
     * @param Game $game
     * @return Player
     * @throws UnableToJoinGameException
     * @throws GameFullException
     */
    public function joinGame(Game $game): Player
    {
        if(count($game->getPlayers()) >= $game->getMap()->getPlayerCount()) {
            throw new GameFullException();
        }

        $mapData = $this->mapDataRetriever->getMapData($game->getMap()->getId());

        if(!$mapData) {
            throw new UnableToJoinGameException("Invalid map data");
        }

        $player = new Player();
        $player->setGame($game);
        $player->setPlayerNumber(count($game->getPlayers()) + 1);

        try {
            $this->entityManager->persist($player);
        } catch (ORMException $e) {
            throw new UnableToJoinGameException("DB error: " . $e->getMessage(), $e->getCode(), $e);
        }

        $this->initializeUnits($mapData, $player);

        try {
            $this->entityManager->flush();
        } catch (ORMException $e) {
            throw new UnableToJoinGameException("DB error: " . $e->getMessage(), $e->getCode(), $e);
        }

        if($player->getPlayerNumber() == $game->getMap()->getPlayerCount()) {
            $this->triggerTurnStartEvent($game);
        }

        return $player;
    }

    /**
     * @param $mapData
     * @param $player
     * @throws UnableToJoinGameException
     */
    private function initializeUnits($mapData, $player): void
    {
        $unitInitializer = new UnitInitializer($mapData);
        $units = $unitInitializer->getUnitsForPlayer($player);

        foreach ($units as $unit) {
            try {
                $this->entityManager->persist($unit);
            } catch (ORMException $e) {
                throw new UnableToJoinGameException("DB error: " . $e->getMessage(), $e->getCode(), $e);
            }
        }
    }
    /**
     * @param string $playerId
     * @return Player|null
     */
    public function getPlayer(string $playerId): ?Player
    {
        return $this->playerRepository->find($playerId);
    }

    /**
     * @param Player $player
     * @return Turn
     * @throws OutOfTurnException
     */
    public function startTurn(Player $player): Turn
    {
        if(!$player->isTurn()) {
            throw new OutOfTurnException();
        }

        $turn = new Turn();
        $turn->setPlayer($player);
        $turn->setStartTimestamp(new \DateTime());
        $turn->setStatus(Turn::STATUS_IN_PROGRESS);

        try {
            $this->entityManager->persist($turn);
            $this->entityManager->flush();

            return $turn;
        } catch (ORMException $e) {
            throw new \RuntimeException("Unable to communicate with DB: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param Turn $turn
     * @throws OutOfTurnException
     * @throws UnplacedUnitsException
     */
    public function endTurn(Turn $turn): void
    {
        if($turn->getStatus() !== Turn::STATUS_IN_PROGRESS) {
            throw new OutOfTurnException();
        }

        $hasUnplacedUnits = false;
        foreach ($turn->getPlayer()->getUnits() as $unit) {
            if($unit->getXPosition() === null || $unit->getYPosition() === null) {
                $hasUnplacedUnits = true;
                break;
            }
        }

        if($hasUnplacedUnits) {
            throw new UnplacedUnitsException();
        }

        $turn->setStatus(Turn::STATUS_COMPLETED);

        try {
            $this->entityManager->flush();
        } catch (ORMException $e) {
            throw new \RuntimeException("Unable to communicate with DB: " . $e->getMessage(), $e->getCode(), $e);
        }

        $game = $turn->getPlayer()->getGame();

        $this->triggerTurnStartEvent($game);
    }
    /**
     * @param string $turnId
     * @return Turn|null
     */
    public function getTurn(string $turnId): ?Turn
    {
        return $this->turnRepository->find($turnId);
    }

    /**
     * @param Game $game
     * @return Unit[]
     */
    public function getUnits(Game $game): array
    {
        $units = [];

        foreach($game->getPlayers() as $player) {
            $units = array_merge($player->getUnits(), $units);
        }

        return $units;
    }

    /**
     * @param string $unitId
     * @return Unit|null
     */
    public function getUnit(string $unitId): ?Unit
    {
        return $this->unitRepository->find($unitId);
    }

    /**
     * @param $game
     */
    private function triggerTurnStartEvent(Game $game): void
    {
        if ($game->getTurnNumber() !== 1) {
            $currentPlayer = null;
            foreach($game->getPlayers() as $player) {
                if ($player->getPlayerNumber() == $game->getPlayerNumber()) {
                    $currentPlayer = $player;
                }
            }

            if($currentPlayer === null){
                throw new \RuntimeException("Missing player #" . $game->getPlayerNumber());
            }

            foreach($currentPlayer->getUnits() as $unit) {
                $unit->regenerateActionPoints();
            }

            try {
                $this->entityManager->flush();
            } catch (ORMException $e) {
                throw new \RuntimeException("Unable to save units: " . $e->getMessage(), $e->getCode(), $e);
            }
        }

        try {
            $this->pusher->setLogger($this->logger);
            $this->pusher->trigger("game-" . $game->getId(), "turn-start", [
                "playerNumber" => $game->getPlayerNumber()
            ]);
        } catch (PusherException $e) {
            $this->logger->error("Exception while firing pusher event: " . $e->getMessage());
        }
    }

    /**
     * @param Turn $turn
     * @param Unit $unit
     * @param array $path
     * @throws OutOfTurnException
     * @throws InsufficientActionPointsException
     * @throws InvalidPathException
     */
    public function moveUnit(Turn $turn, Unit $unit, array $path): void
    {
        if($turn->getStatus() !== Turn::STATUS_IN_PROGRESS || $unit->getPlayerId() !== $turn->getPlayerId()) {
            throw new OutOfTurnException();
        }

        if (count($path) === 0) {
            return;
        }

        $mapData = $this->mapDataRetriever->getMapData($turn->getPlayer()->getGame()->getMap()->getId());

        try {
            /**
             * @var $pathTiles Tile[]
             */
            $pathTiles = array_map(function($path) use ($mapData): Tile {
                return $mapData->getTile($path['x'], $path['y']);
            }, $path);
        } catch (\OutOfBoundsException $outOfBoundsException) {
            throw new InvalidPathException("Unable to be placed out of map");
        }

        if ($unit->getXPosition() === null && $unit->getYPosition() === null) {
            if (count($path) !== 1) {
                throw new InvalidPathException("Unplaced units must be provided a single coordinate.");
            }

            $tile = $pathTiles[0];

            if ($tile->getPlayerOwner() !== $unit->getPlayer()->getPlayerNumber()) {
                throw new InvalidPathException("Unplaced units must be placed on a self owned tile.");
            }

            if($unit->getPlayer()->getGame()->getUnit($path['x'], $path['y'])) {
                throw new InvalidPathException("Tile occupied.");
            }

            $unit->setXPosition($path['x']);
            $unit->setYPosition($path['y']);
        } else {
            
        }
    }
}
