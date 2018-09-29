<?php
namespace App\Game;

use App\Game\Exception\GameFullException;
use App\Game\Exception\OutOfTurnException;
use App\Game\Exception\UnableToJoinGameException;
use App\Game\MapData\MapDataRetriever;
use App\Game\MapData\UnitInitializer;
use App\Orm\Entity\Game;
use App\Orm\Entity\Player;
use App\Orm\Entity\Turn;
use App\Orm\Entity\Unit;
use App\Orm\Repository\GameRepository;
use App\Orm\Repository\MapRepository;
use App\Orm\Repository\PlayerRepository;
use App\Orm\Repository\TurnRepository;
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
     * Manager constructor.
     * @param EntityManager $entityManager
     * @param MapRepository $mapRepository
     * @param GameRepository $gameRepository
     * @param TurnRepository $turnRepository
     * @param PlayerRepository $playerRepository
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
            try {
                $this->pusher->setLogger($this->logger);
                $this->pusher->trigger("game-" . $game->getId(), "turn-start", [
                    "playerNumber" => $game->getPlayerNumber()
                ]);
            } catch (PusherException $e) {
                $this->logger->error("Exception while firing pusher event: " . $e->getMessage());
            }
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
        $turn->setStartTimestamp($now !== null ?: new \DateTime());
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
     */
    public function endTurn(Turn $turn): void
    {
        if($turn->getStatus() !== Turn::STATUS_IN_PROGRESS) {
            throw new OutOfTurnException();
        }

        $turn->setStatus(Turn::STATUS_COMPLETED);

        try {
            $this->entityManager->persist($turn);
            $this->entityManager->flush();
        } catch (ORMException $e) {
            throw new \RuntimeException("Unable to communicate with DB: " . $e->getMessage(), $e->getCode(), $e);
        }

        $game = $turn->getPlayer()->getGame();

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
        // TODO: Implement getUnit() method.
    }

}
