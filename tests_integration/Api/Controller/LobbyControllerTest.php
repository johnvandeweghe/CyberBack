<?php
namespace App\IntegrationTests\Api\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LobbyControllerTest extends WebTestCase
{
    public function testGameCreation()
    {
        $client = self::createClient();

        $client->request("POST", "/game");

        $gameData = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayIsValidGame($gameData);
    }

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
}
