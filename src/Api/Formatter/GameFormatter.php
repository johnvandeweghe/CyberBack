<?php
namespace App\Api\Formatter;

use App\Entity\Game;
use App\MapData\MapDataRetriever;
use App\MapData\Tile;

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
            "playerNumber" => $game->getPlayerNumber(),
            "turnNumber" => $game->getTurnNumber(),
            "map" => [
                "tiles" => array_map(function(Tile $tile) {
                    return [
                        "owner" => $tile->getPlayerOwner(),
                        "type" => $tile->getType(),
                    ];
		}, $mapData->getTiles()),
                "width" => $mapData->getWidth()
            ]
        ]);
    }
}
