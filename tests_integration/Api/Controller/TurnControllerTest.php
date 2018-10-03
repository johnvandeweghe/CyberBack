<?php
namespace App\IntegrationTests\Api\Controller;

use App\Game\Manager;
use App\Orm\Entity\Turn;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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

        $client->request("POST", "/turns", [], [], ["CONTENT_TYPE" => "application/json"], json_encode([
            "gameId" => $gameData["id"],
            "playerId" => $player1Data["id"]
        ]));
        $turn1Data = json_decode($client->getResponse()->getContent(), true);
        $this->assertTurnIsValid($turn1Data);

//        $player1Tiles = array_filter($gameData["map"]["tiles"], function($tile) use ($player1Data) {
//            return $tile["owner"] == $player1Data["playerNumber"];
//        });
        foreach($unitData as $index => $unit) {
            if($unit["coordinates"] === null) {
                $client->request("POST", "/unitAction", [], [], ["CONTENT_TYPE" => "application/json"], json_encode([
                    "turnId" => $turn1Data["id"],
                    "unitId" => $unit["id"],
                    "type" => Manager::UNIT_ACTION_MOVE,
                    "args" => ["path" => [["x" => 0, "y" => 0]]]
                ]));
                $moveResponse = json_decode($client->getResponse()->getContent(), true);
                $this->assertUnitActionIsValid($moveResponse);
            }
        }
    }

    private function assertTurnIsValid($turnData)
    {
        $this->assertArrayHasKey("id", $turnData);
        $this->assertArrayHasKey("status", $turnData);
        $this->assertEquals(Turn::STATUS_IN_PROGRESS, $turnData["status"]);
    }

    private function assertUnitActionIsValid($unitAction)
    {

    }
}
