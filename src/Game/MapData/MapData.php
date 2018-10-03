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

    /**
     * @param int $x
     * @param int $y
     * @return Tile
     * @throws \OutOfBoundsException
     */
    public function getTile(int $x, int $y): Tile
    {
        if($x < 0 || $y < 0) {
            throw new \OutOfBoundsException("Map starts at 0,0");
        }

        if($x >= $this->getWidth()) {
            throw new \OutOfBoundsException("X larger than map width");
        }

        $index = $y * $this->width + $x;

        if ($index >= count($this->getTiles())) {
            throw new \OutOfBoundsException("Y larger than height");
        }

        return $this->getTiles()[$y * $this->width + $x];
    }
}
