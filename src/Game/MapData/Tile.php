<?php
namespace App\Game\MapData;

class Tile
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $playerOwner;

    /**
     * Tile constructor.
     * @param string $type
     * @param int $playerOwner
     */
    public function __construct(string $type, int $playerOwner)
    {
        $this->type = $type;
        $this->playerOwner = $playerOwner;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getPlayerOwner(): int
    {
        return $this->playerOwner;
    }
}
