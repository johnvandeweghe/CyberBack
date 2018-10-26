<?php
namespace App\Api\Controller;

use App\Api\Formatter\MapFormatter;
use App\Game\Exception\GameFullException;
use App\Game\Exception\UnableToJoinGameException;
use App\Game\ManagerInterface;
use App\Api\Formatter\GameFormatter;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LobbyController
{
    /**
     * @var ManagerInterface
     */
    private $manager;

    /**
     * GameController constructor.
     * @param ManagerInterface $manager
     */
    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function getMaps(MapFormatter $mapFormatter): Response
    {
        $maps = $this->manager->getMaps();

        return new Response($mapFormatter->formatMultiple($maps));
    }

    public function createGame(Request $request, GameFormatter $gameFormatter): Response
    {
        $body = json_decode($request->getContent(), true);

        if(!$body || !isset($body['mapId'])) {
            return new Response("Missing mapId field", 400);
        }

        $map = $this->manager->getMap($body['mapId']);

        if(!$map) {
            return new Response("Map not found", 404);
        }

        $game = $this->manager->startGame($map);

        return new Response($gameFormatter->format($game));
    }

    public function getGame($gameId, GameFormatter $gameFormatter): Response
    {
        $game = $this->manager->getGame($gameId);

        if(!$game) {
            return new Response("Game not found", 404);
        }

        return new Response($gameFormatter->format($game));
    }

    public function createPlayer(
        Request $request,
        LoggerInterface $logger
    ): Response
    {
        $body = json_decode($request->getContent(), true);

        if(!$body || !isset($body['gameId'])) {
            return new Response("Missing gameId field", 400);
        }

        $game = $this->manager->getGame($body['gameId']);

        if(!$game) {
            return new Response("Game not found", 404);
        }

        try {
            $player = $this->manager->joinGame($game);
        } catch (GameFullException $e) {
            $logger->warning($e->getMessage());
            return new Response("Game full", 403);
        } catch (UnableToJoinGameException $e) {
            $logger->error($e->getMessage());
            return new Response("Server error", 500);
        }

        return new Response(json_encode(["id" => $player->getId(), "playerNumber" => $player->getPlayerNumber()]));
    }

    public function getPlayer(?string $playerId): Response
    {

        $player = $this->manager->getPlayer($playerId);

        if(!$player) {
            return new Response("Player not found", 404);
        }

        return new Response(json_encode(["id" => $player->getId(), "playerNumber" => $player->getPlayerNumber()]));
    }

}
