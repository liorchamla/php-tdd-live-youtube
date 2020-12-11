<?php

namespace Twitter\Controller;

use Twitter\Http\Response;

class HelloController
{
    public function hello(): Response
    {
        $name = $_GET['name'] ?? "tout le monde";

        return new Response("Bonjour $name");
    }
}
