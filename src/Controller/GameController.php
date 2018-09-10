<?php
namespace App\Controller;

use App\Entity\Game;
use App\Entity\Player;
use App\Formatter\GameFormatter;
use App\Formatter\UnitFormatter;
use App\MapData\MapDataRetriever;
use App\MapData\UnitInitializer;
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

    public function createGame(MapRepository $mapRepository, GameFormatter $gameFormatter): Response
    {
        $map = $mapRepository->chooseRandomMap(2);
        $game = new Game($map);

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return new Response($gameFormatter->format($game));
    }

    public function getGame($gameId, GameFormatter $gameFormatter): Response
    {
        $game = $this->gameRepository->find($gameId);

        if(!$game) {
            return new Response("Game not found", 404);
        }

        return new Response($gameFormatter->format($game));
    }

    public function createPlayer(
        Request $request,
        Pusher $pusher,
        MapDataRetriever $mapDataRetriever
    ): Response
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

        $mapData = $mapDataRetriever->getMapData($game->getMap()->getId());

        if(!$mapData) {
            return new Response("Map data not found", 500);
        }

        $player = new Player();
        $player->setGame($game);
        $player->setPlayerNumber(count($game->getPlayers()) + 1);

        $this->entityManager->persist($player);

        $unitInitializer = new UnitInitializer($mapData);
        $units = $unitInitializer->getUnitsForPlayer($player);

        foreach($units as $unit) {
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

    public function getUnits($gameId, UnitFormatter $unitFormatter): Response
    {
        $game = $this->gameRepository->find($gameId);

        if(!$game) {
            return new Response("Game not found", 404);
        }

        $units = [];

        foreach($game->getPlayers() as $player) {
            $units = array_merge($player->getUnits(), $units);
        }

        return new Response($unitFormatter->format($units));
    }
}
