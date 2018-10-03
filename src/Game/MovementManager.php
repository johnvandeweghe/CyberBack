<?php
namespace App\Game;

use App\Game\Exception\InsufficientActionPointsException;
use App\Game\Exception\InvalidPathException;
use App\Game\MapData\MapDataRetriever;
use App\Game\MapData\Tile;
use App\Orm\Entity\Unit;

class MovementManager
{
    /**
     * @var MapDataRetriever
     */
    private $mapDataRetriever;

    /**
     * MovementManager constructor.
     * @param MapDataRetriever $mapDataRetriever
     */
    public function __construct(MapDataRetriever $mapDataRetriever)
    {
        $this->mapDataRetriever = $mapDataRetriever;
    }

    /**
     * Update a unit's position or blow up if the path is invalid.
     * @param Unit $unit
     * @param array $path
     * @throws InvalidPathException
     */
    public function moveUnplacedUnit(Unit $unit, array $path): void
    {
        if (count($path) !== 1) {
            throw new InvalidPathException("Unplaced units must be provided a single coordinate.");
        }

        $pathTiles = $this->getTiles($unit, $path);

        $tile = $pathTiles[0];

        if ($tile->getPlayerOwner() !== $unit->getPlayer()->getPlayerNumber()) {
            throw new InvalidPathException("Unplaced units must be placed on a self owned tile.");
        }

        if($unit->getPlayer()->getGame()->getUnit($path[0]['x'], $path[0]['y'])) {
            throw new InvalidPathException("Tile occupied.");
        }

        $unit->setXPosition($path[0]['x']);
        $unit->setYPosition($path[0]['y']);
    }

    /**
     * Update a unit's position or blow up if the path is invalid, or they don't have enough action points
     * @param Unit $unit
     * @param array $path
     * @throws InsufficientActionPointsException
     * @throws InvalidPathException
     */
    public function moveUnit(Unit $unit, array $path): void
    {
        //Check each part of the path is a valid tile. Result can be used in future when tiles can affect movement directly.
        $this->getTiles($unit, $path);

        $actionPointCost = ceil(count($path) / $unit->getSpeed());

        if($unit->getCurrentActionPoints() < $actionPointCost) {
            throw new InsufficientActionPointsException("Cost: $actionPointCost, Available: {$unit->getCurrentActionPoints()}");
        }

        $currentPosition = ["x" => $unit->getXPosition(), "y" => $unit->getYPosition()];
        foreach ($path as $index => $pathPart) {
            $xDifference = abs($pathPart["x"] - $currentPosition["x"]);
            $yDifference = abs($pathPart["y"] - $currentPosition["y"]);

            //Can only move one square at a time
            if($xDifference + $yDifference !== 1) {
                throw new InvalidPathException("Each part of the path needs to be one square apart.");
            }

            if($unit->getPlayer()->getGame()->getUnit($pathPart['x'], $pathPart['y'])) {
                throw new InvalidPathException("Tile occupied.");
            }

            $currentPosition = $pathPart;
        }

        $unit->setCurrentActionPoints($unit->getCurrentActionPoints() - $actionPointCost);
        $unit->setXPosition($currentPosition['x']);
        $unit->setYPosition($currentPosition['y']);
    }

    /**
     * @param Unit $unit
     * @param array $path
     * @return Tile[]
     * @throws InvalidPathException
     */
    protected function getTiles(Unit $unit, $path): array {
        $mapData = $this->mapDataRetriever->getMapData($unit->getPlayer()->getGame()->getMap()->getId());

        try {
            /**
             * @var $pathTiles Tile[]
             */
            return array_map(function($path) use ($mapData): Tile {
                return $mapData->getTile($path['x'], $path['y']);
            }, $path);
        } catch (\OutOfBoundsException $outOfBoundsException) {
            throw new InvalidPathException("Unable to be placed out of map");
        }
    }
}
