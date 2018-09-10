<?php
namespace App\Formatter;

use App\Entity\Game;
use App\MapData\MapDataRetriever;

class GameFormatter
{
    /**
     * @var MapDataRetriever
     */
    private $mapDataRetriever;

    /**
     * GameFormatter constructor.
     * @param MapDataRetriever $mapDataRetriever
     */
    public function __construct(MapDataRetriever $mapDataRetriever)
    {
        $this->mapDataRetriever = $mapDataRetriever;
    }

    public function format(Game $game): string
    {
        $mapData = $this->mapDataRetriever->getMapData($game->getMap()->getId());

        return json_encode([
            "id" => $game->getId(),
            "map" => [
                "tiles" => $mapData->getTiles(),
                "width" => $mapData->getWidth()
            ]
        ]);
    }
}
