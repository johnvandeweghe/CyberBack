<?php
namespace App\Controller;

use App\Entity\Turn;
use App\Repository\GameRepository;
use App\Repository\PlayerRepository;
use App\Repository\TurnRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TurnController
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

    public function createTurn(Request $request, PlayerRepository $playerRepository, TurnRepository $turnRepository): Response
    {
        $body = json_decode($request->getContent(), true);
        $playerId = $body["playerId"] ?? null;
        if(!$playerId) {
            return new Response("Missing player id", Response::HTTP_BAD_REQUEST);
        }

        $player = $playerRepository->find($playerId);

        if($player) {
            return new Response("Player not found", Response::HTTP_NOT_FOUND);
        }

        if(!$player->isTurn()) {
            return new Response("Not your turn", Response::HTTP_FORBIDDEN);
        }

        $turn = $turnRepository->startTurn($player);

        $this->entityManager->persist($turn);
        $this->entityManager->flush();

        return new Response(json_encode([
            "id" => $turn->getId(),
            "status" => $turn->getStatus()
        ]));
    }

    public function updateTurn(Request $request, TurnRepository $turnRepository): Response
    {
        $body = json_decode($request->getContent(), true);
        $turnId = $body["turnId"] ?? null;
        $status = $body["status"] ?? null;

        if(!$turnId) {
            return new Response("Missing turn id", Response::HTTP_BAD_REQUEST);
        }
        if($status !== Turn::STATUS_COMPLETED) {
            return new Response("Bad status value", Response::HTTP_BAD_REQUEST);
        }

        $turn = $turnRepository->find($turnId);

        if($turn) {
            return new Response("Turn not found", Response::HTTP_NOT_FOUND);
        }

        if($turn->getStatus() !== Turn::STATUS_IN_PROGRESS) {
            return new Response("Not your turn", Response::HTTP_FORBIDDEN);
        }

        $turn->setStatus(Turn::STATUS_COMPLETED);

        $this->entityManager->persist($turn);
        $this->entityManager->flush();

        return new Response(json_encode([
            "id" => $turn->getId(),
            "status" => $turn->getStatus()
        ]));
    }

    //TODO:
    public function createUnitAction()
    {

    }
}
