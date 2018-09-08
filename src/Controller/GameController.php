<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GameController
{

    public function create(Request $request): Response
    {
        return new Response("hello world");
    }
}
