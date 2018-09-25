<?php
namespace App\MapData;

use App\Entity\Player;
use App\Entity\Unit;

class UnitInitializer
{
    /**
     * @var MapData
     */
    private $mapData;

    /**
     * UnitInitializer constructor.
     * @param MapData $mapData
     */
    public function __construct(MapData $mapData)
    {
        $this->mapData = $mapData;
    }

    /**
     * @param Player $player
     * @return Unit[]
     */
    public function getUnitsForPlayer(Player $player): array
    {
        $playerUnitData = array_filter($this->mapData->getUnitData(), function ($unitDatum) use ($player) {
            return $unitDatum["playerOwner"] != $player->getPlayerNumber();
        });

        return array_map(function ($unitDatum) use ($player) {
            $unit = new Unit();
            $unit->setPlayer($player);
            $unit->setSpeed($unitDatum["speed"]);
            $unit->setHealth($unitDatum["health"]);
            $unit->setAttack($unitDatum["attack"]);
            $unit->setDefense($unitDatum["defense"]);
            $unit->setMaxRange($unitDatum["maxRange"]);
            $unit->setMinRange($unitDatum["minRange"]);
            $unit->setMaxActionPoints($unitDatum["maxAP"]);
            $unit->setCurrentActionPoints($unitDatum["currentAP"]);
            $unit->setActionPointRegenRate($unitDatum["apRegen"]);
            $unit->setUnitType($unitDatum["unitType"]);

            return $unit;
        }, $playerUnitData);
    }
}
