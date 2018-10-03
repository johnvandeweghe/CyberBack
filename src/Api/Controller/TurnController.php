<?php
namespace App\Api\Controller;

use App\Game\Exception\InsufficientActionPointsException;
use App\Game\Exception\InvalidPathException;
use App\Game\Exception\OutOfTurnException;
use App\Game\Exception\UnplacedUnitsException;
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
        } catch (UnplacedUnitsException $e) {
            return new Response("Not your turn", Response::HTTP_BAD_REQUEST);
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

        $turn = $this->manager->getTurn($turnId);

        if(!$turn) {
            return new Response("Turn not found", Response::HTTP_NOT_FOUND);
        }

        $unit = $this->manager->getUnit($unitId);

        if(!$unit) {
            return new Response("Unit not found", Response::HTTP_NOT_FOUND);
        }

        try {
            switch($type) {
                case Manager::UNIT_ACTION_MOVE:
                    $this->manager->moveUnit($turn, $unit, $args["path"] ?? []);
                    return new Response(json_encode([
                        "unitId" => $unit->getId(),
                        "turnId" => $turn->getId(),
                        "type" => Manager::UNIT_ACTION_MOVE,
                        "status" => "success",
                        "affectedUnitIds" => [
                            $unit->getId()
                        ]
                    ]));
                    break;
                case Manager::UNIT_ACTION_ATTACK:
                    return new Response("Not implemented", Response::HTTP_NOT_IMPLEMENTED);
                    break;
                default:
                    return new Response("Unknown Type", Response::HTTP_NOT_FOUND);
            }
        } catch (OutOfTurnException $e) {
            return new Response("Not your turn", Response::HTTP_FORBIDDEN);
        } catch (InsufficientActionPointsException $e) {
            return new Response("Insufficient action points: " . $e->getMessage(), Response::HTTP_FORBIDDEN);
        } catch (InvalidPathException $e) {
            return new Response("Invalid path: " . $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
