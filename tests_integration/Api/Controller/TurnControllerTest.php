<?php
namespace App\IntegrationTests\Api\Controller;

use App\Game\Manager;
use App\Orm\Entity\Turn;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TurnControllerTest extends WebTestCase
{
    public function testCanTakeInitialTurn()
    {
        $client = self::createClient();

        $client->request("POST", "/game");
        $gameData = json_decode($client->getResponse()->getContent(), true);
        $client->request("POST", "/player", [], [], ["CONTENT_TYPE" => "application/json"], json_encode(["gameId" => $gameData["id"]]));
        $player1Data = json_decode($client->getResponse()->getContent(), true);
        $client->request("POST", "/player", [], [], ["CONTENT_TYPE" => "application/json"], json_encode(["gameId" => $gameData["id"]]));
        $player2Data = json_decode($client->getResponse()->getContent(), true);
        $client->request("GET", "/units/{$gameData["id"]}");
        $unitData = json_decode($client->getResponse()->getContent(), true);

        $this->takeFirstTurn($client, $gameData, $player1Data, $unitData);

        $this->takeFirstTurn($client, $gameData, $player2Data, $unitData);
    }

    private function getOwnedTilesWithCoordinates($tiles, $width, $playerNumber)
    {
        //Add coordinates, and filter to tiles that are owned by $playerNumber
        return array_values(array_filter(
            array_map(
                function($tile, $index) use ($width) {
                    return array_merge($tile, [
                        "x" => $index % $width,
                        "y" => floor($index / $width)
                    ]);
                },
                $tiles,
                array_keys($tiles)
            ),
            function($tile) use ($playerNumber) {
                return $tile["owner"] == $playerNumber;
            }
        ));
    }

    private function assertTurnIsValid($turnData)
    {
        $this->assertArrayHasKey("id", $turnData);
        $this->assertArrayHasKey("status", $turnData);
        $this->assertContains($turnData["status"], [Turn::STATUS_IN_PROGRESS, Turn::STATUS_COMPLETED]);
    }

    private function assertUnitMoveActionIsValid($unitAction)
    {
        $this->assertArrayHasKey("unitId", $unitAction);
        $this->assertArrayHasKey("turnId", $unitAction);
        $this->assertArrayHasKey("type", $unitAction);
        $this->assertEquals(Manager::UNIT_ACTION_MOVE, $unitAction["type"]);
        $this->assertEquals("success", $unitAction["status"]);
        $this->assertArrayHasKey("affectedUnitIds", $unitAction);
        $this->assertCount(1, $unitAction["affectedUnitIds"]);
    }

    /**
     * @param $gameData
     * @param $playerData
     * @param $unitData
     * @param \Symfony\Bundle\FrameworkBundle\Client $client
     * @param $turnData
     */
    private function placeAllUnitsForPlayer($gameData, $playerData, $unitData, $client, $turnData): void
    {
        $playerTiles = $this->getOwnedTilesWithCoordinates($gameData["map"]["tiles"], $gameData["map"]["width"],
            $playerData["playerNumber"]);

        $targetTileIndex = 0;

        foreach ($unitData as $index => $unit) {
            if ($unit["coordinates"] === null && $unit["owner"] === $playerData["playerNumber"]) {
                $client->request("POST", "/unitAction", [], [], ["CONTENT_TYPE" => "application/json"], json_encode([
                    "turnId" => $turnData["id"],
                    "unitId" => $unit["id"],
                    "type" => Manager::UNIT_ACTION_MOVE,
                    "args" => [
                        "path" => [
                            [
                                "x" => $playerTiles[$targetTileIndex]["x"],
                                "y" => $playerTiles[$targetTileIndex]["y"]
                            ]
                        ]
                    ]
                ]));
                $targetTileIndex++;
                $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
                $moveResponse = json_decode($client->getResponse()->getContent(), true);
                $this->assertUnitMoveActionIsValid($moveResponse);
            }
        }
    }

    /**
     * @param \Symfony\Bundle\FrameworkBundle\Client $client
     * @param $gameData
     * @param $playerData
     * @param $unitData
     */
    private function takeFirstTurn($client, $gameData, $playerData, $unitData): void
    {
        $client->request("POST", "/turns", [], [], ["CONTENT_TYPE" => "application/json"], json_encode([
            "gameId" => $gameData["id"],
            "playerId" => $playerData["id"]
        ]));
        $turnData = json_decode($client->getResponse()->getContent(), true);
        $this->assertTurnIsValid($turnData);

        $this->placeAllUnitsForPlayer($gameData, $playerData, $unitData, $client, $turnData);

        $client->request("PATCH", "/turns/{$turnData["id"]}", [], [], ["CONTENT_TYPE" => "application/json"],
            json_encode([
                "status" => Turn::STATUS_COMPLETED
            ]));
        $turnData = json_decode($client->getResponse()->getContent(), true);
        $this->assertTurnIsValid($turnData);
    }
}
