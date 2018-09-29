<?php
namespace App\Api\Formatter;

use App\Orm\Entity\Unit;

class UnitFormatter
{
    /**
     * @param Unit[] $units
     * @return string
     */
    public function format(array $units): string
    {
        return json_encode(array_map([$this, "formatOneArray"], $units));
    }

    /**
     * @param Unit $unit
     * @return string
     */
    public function formatOne(Unit $unit): string
    {
        return json_encode($this->formatOneArray($unit));
    }

    /**
     * @param Unit $unit
     * @return array
     */
    protected function formatOneArray(Unit $unit): array
    {
        return [
            "id" => $unit->getId(),
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
    }
}
