<?php
namespace App\UnitTests\Game;

use App\Game\Exception\InvalidPathException;
use App\Game\MapData\MapData;
use App\Game\MapData\MapDataRetriever;
use App\Game\MapData\Tile;
use App\Game\MovementManager;
use App\Orm\Entity\Game;
use App\Orm\Entity\Map;
use App\Orm\Entity\Player;
use App\Orm\Entity\Unit;
use PHPUnit\Framework\TestCase;

class MovementManagerTest extends TestCase
{
    public function testPlacingUnplacedUnitWithMultiplePathThrowsException()
    {
        $mapDataRetrieverMock = $this->getMockBuilder(MapDataRetriever::class)->disableOriginalConstructor()->getMock();
        $unit = new Unit();

        $movementManager = new MovementManager($mapDataRetrieverMock);

        $this->expectException(InvalidPathException::class);

        $movementManager->moveUnplacedUnit($unit, [["x" => 1, "y" => 1], ["x" => 2, "y" => 1]]);
    }

    public function testPlacingUnplacedUnitOnNonExistentTileThrowsException()
    {
        $mapId = "asdasdasdarewgr";
        $mapDataRetrieverMock = $this->getMockBuilder(MapDataRetriever::class)->disableOriginalConstructor()->getMock();
        $mapDataRetrieverMock->method('getMapData')->with($mapId)->willReturn(new MapData([], 0, []));
        $mapMock = $this->getMockBuilder(Map::class)->getMock();
        $mapMock->method('getId')->willReturn($mapId);
        $unit = (new Unit())->setPlayer((new Player())->setGame(new Game($mapMock)));


        $movementManager = new MovementManager($mapDataRetrieverMock);

        $this->expectException(InvalidPathException::class);

        $movementManager->moveUnplacedUnit($unit, [["x" => 1, "y" => 1]]);
    }

    public function testPlacingUnplacedUnitOnUnownedTileThrowsException()
    {
        $mapId = "asdasdasdarewgr";
        $mapDataRetrieverMock = $this->getMockBuilder(MapDataRetriever::class)->disableOriginalConstructor()->getMock();
        $mapDataRetrieverMock->method('getMapData')->with($mapId)->willReturn(new MapData([
            new Tile("", 123)
        ], 1, []));
        $mapMock = $this->getMockBuilder(Map::class)->getMock();
        $mapMock->method('getId')->willReturn($mapId);
        $unit = (new Unit())->setPlayer((new Player())->setPlayerNumber(321)->setGame(new Game($mapMock)));

        $movementManager = new MovementManager($mapDataRetrieverMock);

        $this->expectException(InvalidPathException::class);

        $movementManager->moveUnplacedUnit($unit, [["x" => 0, "y" => 0]]);
    }

    public function testPlacingUnplacedUnitOnOccupiedTileThrowsException()
    {
        $mapId = "asdasdasdarewgr";
        $mapDataRetrieverMock = $this->getMockBuilder(MapDataRetriever::class)->disableOriginalConstructor()->getMock();
        $mapDataRetrieverMock->method('getMapData')->with($mapId)->willReturn(new MapData([
            new Tile("", 123)
        ], 1, []));
        $mapMock = $this->getMockBuilder(Map::class)->getMock();
        $mapMock->method('getId')->willReturn($mapId);
        $gameMock = $this->getMockBuilder(Game::class)->disableOriginalConstructor()->getMock();
        $gameMock->method('getMap')->willReturn($mapMock);
        $gameMock->method('getUnit')->willReturn((new Unit())->setXPosition(0)->setYPosition(0));
        $unit = (new Unit())->setPlayer((new Player())->setPlayerNumber(123)->setGame($gameMock));

        $movementManager = new MovementManager($mapDataRetrieverMock);

        $this->expectException(InvalidPathException::class);

        $movementManager->moveUnplacedUnit($unit, [["x" => 0, "y" => 0]]);
    }

    public function testPlacingUnplacedUnitSetsUnitPosition()
    {
        $mapId = "asdasdasdarewgr";
        $targetX = 0;
        $targetY = 0;
        $mapDataRetrieverMock = $this->getMockBuilder(MapDataRetriever::class)->disableOriginalConstructor()->getMock();
        $mapDataRetrieverMock->method('getMapData')->with($mapId)->willReturn(new MapData([
            new Tile("", 123)
        ], 1, []));
        $mapMock = $this->getMockBuilder(Map::class)->getMock();
        $mapMock->method('getId')->willReturn($mapId);
        $unit = (new Unit())->setPlayer((new Player())->setPlayerNumber(123)->setGame(new Game($mapMock)));

        $movementManager = new MovementManager($mapDataRetrieverMock);

        $movementManager->moveUnplacedUnit($unit, [["x" => $targetX, "y" => $targetY]]);

        $this->assertEquals($targetX, $unit->getXPosition());
        $this->assertEquals($targetY, $unit->getYPosition());
    }
}
