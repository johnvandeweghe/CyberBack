<?php
namespace App\Api\Controller;

use Symfony\Component\HttpFoundation\Response;

class DefaultController
{
    public function index(): Response
    {
        return new Response("OK Jon");
    }
}
