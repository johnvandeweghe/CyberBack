<?php

namespace App\Orm\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class LobbyManager
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $callbackUrl;

    /**
     * @ORM\Column(type="string")
     */
    private $secret;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCallbackUrl(): string
    {
        return $this->callbackUrl;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

}
