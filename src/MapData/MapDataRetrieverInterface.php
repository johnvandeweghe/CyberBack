<?php
namespace App\MapData;

interface MapDataRetrieverInterface
{
    public function getMapData(string $mapId): ?MapData;
}
