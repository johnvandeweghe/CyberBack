<?php
namespace App\IntegrationTests\Api\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LobbyControllerTest extends WebTestCase
{
    public function testJoiningGame()
    {
        $client = self::createClient();

        $client->request("POST", "/game", [], [], ["CONTENT_TYPE" => "application/json"], json_encode(["mapId" => "AE7E7566-5105-47DD-8438-3BEF9524A1AC"]));
        $gameData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayIsValidGame($gameData);

        $client->request("GET", "/game/{$gameData["id"]}");
        $gameData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayIsValidGame($gameData);

        $client->request("POST", "/player", [], [], ["CONTENT_TYPE" => "application/json"], json_encode(["gameId" => $gameData["id"]]));
        $playerData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayIsValidPlayer($playerData);

        $client->request("GET", "/player/{$playerData["id"]}");
        $playerData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayIsValidPlayer($playerData);
    }

    //TODO: Test non 200 responses

    private function assertArrayIsValidGame(array $gameData): void
    {
        $this->assertArrayHasKey("id", $gameData);
        $this->assertArrayHasKey("playerNumber", $gameData);
        $this->assertArrayHasKey("turnNumber", $gameData);
        $this->assertArrayHasKey("map", $gameData);
        $this->assertArrayHasKey("tiles", $gameData["map"] ?? []);
        $this->assertArrayHasKey("width", $gameData["map"] ?? []);
        $this->assertGreaterThanOrEqual($gameData["map"]["width"] ?? PHP_INT_MAX, count($gameData["map"]["tiles"] ?? []));
        $this->assertArrayHasKey(0, $gameData["map"]["tiles"] ?? []);
        $this->assertArrayHasKey("type", $gameData["map"]["tiles"][0] ?? []);
        $this->assertArrayHasKey("owner", $gameData["map"]["tiles"][0] ?? []);
    }

    private function assertArrayIsValidPlayer(array $playerData): void
    {
        $this->assertArrayHasKey("id", $playerData);
        $this->assertArrayHasKey("playerNumber", $playerData);
    }
}
