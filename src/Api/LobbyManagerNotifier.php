<?php
namespace App\Api;

use App\Api\Formatter\GameFormatter;
use App\Game\LobbyManagerNotifierInterface;
use App\Orm\Entity\Game;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

class LobbyManagerNotifier implements LobbyManagerNotifierInterface
{
    /**
     * @var ClientInterface
     */
    private $client;
    /**
     * @var GameFormatter
     */
    private $gameFormatter;

    /**
     * LobbyMangerNotifier constructor.
     * @param Client $client
     * @param GameFormatter $gameFormatter
     */
    public function __construct(Client $client, GameFormatter $gameFormatter)
    {
        $this->client = $client;
        $this->gameFormatter = $gameFormatter;
    }

    /**
     * @param Game $game
     * @throws \RuntimeException
     */
    public function notifyLobbyManager(Game $game): void
    {
        $lobbyManager = $game->getMap()->getLobbyManager();

        if(!$lobbyManager) {
            return;
        }

        $url = $lobbyManager->getCallbackUrl();
        $secret = $lobbyManager->getSecret();

        try {
            $data = $this->gameFormatter->format($game);
            $this->client->request(
                "POST",
                $url,
                [
                    "headers" => [
                        "Content-Type" => [
                            "application/json"
                        ],
                        "X-Signature" => [
                            hash_hmac("sha256", $data, $secret, false)
                        ]
                    ],
                    "body" => $data
                ]
            );
        } catch (GuzzleException $e) {
            throw new \RuntimeException("Lobby error: " . $e->getMessage(), $e->getCode(), $e);
        }
    }
}
