<?php
namespace App\Game\MapData;

class MapData
{
    /**
     * @var Tile[]
     */
    private $tiles;
    /**
     * @var int
     */
    private $width;
    /**
     * @var array
     */
    private $unitData;

    /**
     * MapData constructor.
     * @param Tile[] $tiles
     * @param int $width
     * @param array $unitData
     */
    public function __construct(
        array $tiles,
        int $width,
        array $unitData
    )
    {
        $this->tiles = $tiles;
        $this->width = $width;
        $this->unitData = $unitData;
    }

    /**
     * @return Tile[]
     */
    public function getTiles(): array
    {
        return $this->tiles;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @return array
     */
    public function getUnitData(): array
    {
        return $this->unitData;
    }
}
