<?php
namespace App\Api\Formatter;

use App\Orm\Entity\Game;
use App\Game\MapData\MapDataRetriever;
use App\Game\MapData\Tile;

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
