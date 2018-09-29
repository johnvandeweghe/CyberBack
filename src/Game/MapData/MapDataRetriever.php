<?php
namespace App\Game\MapData;

use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class MapDataRetriever implements MapDataRetrieverInterface
{
    /**
     * @var Finder
     */
    private $finder;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * MapDataRetriever constructor.
     * @param LoggerInterface $logger
     */
    const ASSETS_MAPS_DIR = "../../assets/maps/";

    public function __construct(LoggerInterface $logger, ?Finder $finder = null)
    {
        if ($finder) {
            $this->finder = $finder;
        } else {
            $this->finder = Finder::create();
            $this->finder->in(self::ASSETS_MAPS_DIR)->files();
        }
        $this->logger = $logger;
    }

    /**
     * @param string $mapId
     * @return MapData|null
     */
    public function getMapData(string $mapId): ?MapData
    {
        $iterator = $this->finder->name($mapId . ".json")->getIterator();
        $iterator->rewind();
        /**
         * @var $mapDataFileInfo SplFileInfo
         */
        $mapDataFileInfo = $iterator->current();

        $this->logger->debug($mapDataFileInfo->getFilename());
        if(!$mapDataFileInfo) {
            return null;
        }

        $mapData = json_decode($mapDataFileInfo->getContents(), true);

        return new MapData(
            array_map(function($tileDatum) {
                return new Tile($tileDatum["type"], $tileDatum["owner"]);
            }, $mapData["tiles"]),
            $mapData["width"],
            $mapData["units"]
        );
    }
}
