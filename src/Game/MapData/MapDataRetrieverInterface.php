<?php
namespace App\Game\MapData;

interface MapDataRetrieverInterface
{
    public function getMapData(string $mapId): ?MapData;
}
