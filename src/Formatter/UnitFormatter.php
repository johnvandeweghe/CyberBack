<?php
namespace App\Formatter;

use App\Entity\Unit;

class UnitFormatter
{
    /**
     * @param Unit[] $units
     * @return string
     */
    public function format(array $units): string
    {
        return json_encode(array_map(function(Unit $unit) {
            return [
                "attack" => $unit->getAttack(),
                "defense" => $unit->getDefense(),
                "health" => $unit->getHealth(),
                "unitType" => $unit->getUnitType(),
                "minRange" => $unit->getMinRange(),
                "maxRange" => $unit->getMaxRange(),
                "owner" => $unit->getPlayer()->getPlayerNumber(),
                "coordinates" => $unit->getXPosition() && $unit->getYPosition() ? [
                    "x" => $unit->getXPosition(),
                    "y" => $unit->getYPosition()
                ] : null
            ];
        }, $units));
    }
}
