<?php
namespace App\Controller;

use App\Formatter\UnitFormatter;
use App\Repository\GameRepository;
use App\Repository\UnitRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    public function getUnit($unitId, UnitRepository $unitRepository ,UnitFormatter $unitFormatter): Response
    {
        $unit = $unitRepository->find($unitId);

        if(!$unit) {
            return new Response("Unit not found", 404);
        }

        return new Response($unitFormatter->formatOne($unit));
    }
}
