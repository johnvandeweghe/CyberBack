<?php
namespace App\Api\Controller;

use App\Api\Formatter\UnitFormatter;
use App\Game\ManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class GameController
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

    public function getUnits($gameId, UnitFormatter $unitFormatter): Response
    {
        $game = $this->manager->getGame($gameId);

        if(!$game) {
            return new Response("Game not found", 404);
        }

        $units = $this->manager->getUnits($game);

        return new Response($unitFormatter->format($units));
    }

    public function getUnit($unitId, UnitFormatter $unitFormatter): Response
    {
        $unit = $this->manager->getUnit($unitId);

        if(!$unit) {
            return new Response("Unit not found", 404);
        }

        return new Response($unitFormatter->formatOne($unit));
    }
}
