<?php
namespace App\Api\Controller;

use App\Game\Exception\OutOfTurnException;
use App\Game\Manager;
use App\Game\ManagerInterface;
use App\Orm\Entity\Turn;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TurnController
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

    public function createTurn(Request $request): Response
    {
        $body = json_decode($request->getContent(), true);
        $playerId = $body["playerId"] ?? null;

        if(!$playerId) {
            return new Response("Missing player id", Response::HTTP_BAD_REQUEST);
        }

        $player = $this->manager->getPlayer($playerId);

        if(!$player) {
            return new Response("Player not found", Response::HTTP_NOT_FOUND);
        }

        try {
            $turn = $this->manager->startTurn($player);
        } catch (OutOfTurnException $e) {
            return new Response("Not your turn", Response::HTTP_FORBIDDEN);
        }

        return new Response(json_encode([
            "id" => $turn->getId(),
            "status" => $turn->getStatus()
        ]));
    }

    public function updateTurn(?string $turnId, Request $request): Response
    {
        $body = json_decode($request->getContent(), true);
        $status = $body["status"] ?? null;

        if(!$turnId) {
            return new Response("Missing turn id", Response::HTTP_BAD_REQUEST);
        }
        if($status !== Turn::STATUS_COMPLETED) {
            return new Response("Bad status value", Response::HTTP_BAD_REQUEST);
        }

        $turn = $this->manager->getTurn($turnId);

        if(!$turn) {
            return new Response("Turn not found", Response::HTTP_NOT_FOUND);
        }

        try {
            $this->manager->endTurn($turn);
        } catch (OutOfTurnException $e) {
            return new Response("Not your turn", Response::HTTP_FORBIDDEN);
        }

        return new Response(json_encode([
            "id" => $turn->getId(),
            "status" => $turn->getStatus()
        ]));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function createUnitAction(Request $request)
    {
        $body = json_decode($request->getContent(), true);
        $turnId = $body["turnId"] ?? null;
        $unitId = $body["unitId"] ?? null;
        $type = $body["type"] ?? null;
        $args = $body["args"] ?? null;

        if(!$turnId || !$unitId) {
            return new Response("Missing turn id or unit id", Response::HTTP_BAD_REQUEST);
        }

        if(!$type) {
            return new Response("Missing action type", Response::HTTP_BAD_REQUEST);
        }

        switch($type) {
            case Manager::UNIT_ACTION_MOVE:

                break;
            case Manager::UNIT_ACTION_ATTACK:
                break;
            default:
                return new Response("Unknown Type", Response::HTTP_NOT_FOUND);
        }
    }
}
