<?php
namespace App\IntegrationTests\Api\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GameControllerTest extends WebTestCase
{
    public function testGetUnits()
    {
        $client = self::createClient();

        $client->request("POST", "/game");
        $gameData = json_decode($client->getResponse()->getContent(), true);
        $client->request("POST", "/player", [], [], ["CONTENT_TYPE" => "application/json"], json_encode(["gameId" => $gameData["id"]]));
        $client->request("GET", "/units/{$gameData["id"]}");
        $unitData = json_decode($client->getResponse()->getContent(), true);

        foreach($unitData as $datum) {
            $this->assertUnitDataIsValid($datum);
        }

        $client->request("GET", "/unit/{$unitData[0]["id"]}");
        $unitDatum = json_decode($client->getResponse()->getContent(), true);
        $this->assertUnitDataIsValid($unitDatum);
    }

    //TODO: Test non 200 responses

    private function assertUnitDataIsValid($unitData)
    {
        $this->assertArrayHasKey("id", $unitData);
        $this->assertArrayHasKey("attack", $unitData);
        $this->assertArrayHasKey("defense", $unitData);
        $this->assertArrayHasKey("health", $unitData);
        $this->assertArrayHasKey("unitType", $unitData);
        $this->assertArrayHasKey("minRange", $unitData);
        $this->assertArrayHasKey("maxRange", $unitData);
        $this->assertArrayHasKey("maxAP", $unitData);
        $this->assertArrayHasKey("currentAP", $unitData);
        $this->assertArrayHasKey("apRegen", $unitData);
        $this->assertArrayHasKey("owner", $unitData);
        $this->assertArrayHasKey("coordinates", $unitData);
        $this->assertNull($unitData["coordinates"]);
    }
}
