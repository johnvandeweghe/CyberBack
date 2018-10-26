<?php
namespace App\Api\Formatter;

use App\Orm\Entity\Map;

/**
 * Class MapFormatter
 * @package App\Api\Formatter
 */
class MapFormatter
{
    /**
     * @param Map $map
     * @return string
     */
    public function format(Map $map): string
    {
        return json_encode($this->mapToArray($map));
    }

    /**
     * @param Map[] $maps
     * @return string
     */
    public function formatMultiple(array $maps): string
    {
        return json_encode(array_map(function(Map $map): array {
            return $this->mapToArray($map);
        }, $maps));
    }

    /**
     * @param Map $map
     * @return array
     */
    private function mapToArray(Map $map): array
    {
        return [
            "id" => $map->getId(),
            "numberOfPlayers" => $map->getPlayerCount(),
            "lobbyService" => $map->getLobbyManagerName()
        ];
    }
}
