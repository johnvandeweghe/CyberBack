<?php
namespace App\Controller;

use App\Entity\Game;
use App\Entity\Player;
use App\Entity\Unit;
use App\MapData\MapDataRetriever;
use App\Repository\GameRepository;
use App\Repository\MapRepository;
use Doctrine\ORM\EntityManagerInterface;
use Pusher\Pusher;
use Pusher\PusherException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GameController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var GameRepository
     */
    private $gameRepository;

    /**
     * GameController constructor.
     * @param EntityManagerInterface $entityManager
     * @param GameRepository $gameRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        GameRepository $gameRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->gameRepository = $gameRepository;
    }

    public function createGame(MapRepository $mapRepository): Response
    {
        $map = $mapRepository->chooseRandomMap(2);
        $game = new Game($map);

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return new Response(json_encode(["id" => $game->getId()]));
    }

    public function getGame($gameId): Response
    {
        $game = $this->gameRepository->find($gameId);

        if(!$game) {
            return new Response("Game not found", 404);
        }

        return new Response(json_encode(["id" => $game->getId()]));
    }

    public function createPlayer(Request $request, Pusher $pusher, MapDataRetriever $mapDataRetriever): Response
    {
        $body = json_decode($request->getContent(), true);

        if(!$body || !isset($body['gameId'])) {
            return new Response("Missing gameId field", 400);
        }

        $game = $this->gameRepository->find($body['gameId']);

        if(!$game) {
            return new Response("Game not found", 404);
        }


        if($game->getPlayers() >= $game->getMap()->getPlayerCount()) {
            return new Response("Game full", 403);
        }

        $player = new Player();
        $player->setGame($game);
        $player->setPlayerNumber(count($game->getPlayers()) + 1);

        $this->entityManager->persist($player);

        $mapData = $mapDataRetriever->getMapData($game->getMap()->getId());

        if(!$mapData) {
            return new Response("Map data not found", 500);
        }

        foreach($mapData->getUnitData() as $unitDatum) {
            if($unitDatum["playerOwner"] != $player->getPlayerNumber()){
                continue;
            }

            $unit = new Unit();
            $unit->setPlayer($player);
            $unit->setSpeed($unitDatum["speed"]);
            $unit->setHealth($unitDatum["health"]);
            $unit->setAttack($unitDatum["attack"]);
            $unit->setDefense($unitDatum["defense"]);
            $unit->setMaxRange($unitDatum["maxRange"]);
            $unit->setMinRange($unitDatum["minRange"]);
            $unit->setUnitType($unitDatum["unitType"]);
            $this->entityManager->persist($unit);
        }

        $this->entityManager->flush();

        if($player->getPlayerNumber() == $game->getMap()->getPlayerCount()) {
            try {
                $pusher->trigger("game-{$game->getId()}", "game-start",
                    ["mapId" => $game->getMapId()]);
            } catch (PusherException $e) {
            }
        }

        return new Response(json_encode(["id" => $player->getId(), "playerNumber" => $player->getPlayerNumber()]));
    }

    public function getMap($mapId, MapRepository $mapRepository, MapDataRetriever $mapDataRetriever) {
        $map = $mapRepository->find($mapId);

        if(!$map) {
            return new Response("Map not found", 404);
        }
        $mapData = $mapDataRetriever->getMapData($map->getId());

        return new Response(json_encode([
            "tiles" => $mapData->getTiles(),
            "width" => $mapData->getWidth()
        ]));
    }

    public function getUnits($gameId): Response
    {
        $game = $this->gameRepository->find($gameId);

        if(!$game) {
            return new Response("Game not found", 404);
        }

        $units = [];

        foreach($game->getPlayers() as $player) {
            $units = array_merge($player->getUnits(), $units);
        }

        return new Response(json_encode(array_map(function(Unit $unit) {
            return [
                "attack" => $unit->getAttack(),
                "defense" => $unit->getDefense(),
                "health" => $unit->getHealth(),
                "unitType" => $unit->getUnitType(),
                "minRange" => $unit->getMinRange(),
                "maxRange" => $unit->getMaxRange(),
                "owner" => $unit->getPlayer()->getPlayerNumber(),
                "coordinates" => $unit->getXPosition() && $unit->getYPosition() ? [
                    "x" => $unit->getXPosition(),
                    "y" => $unit->getYPosition()
                ] : null
            ];
        }, $units)));
    }
}
