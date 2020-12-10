<?php

use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    public function test_homepage_says_hello()
    {
        // Etant donné une requête HTTP avec un paramètre name qui vaut Lior
        $_GET['name'] = "Lior";

        // Quand j'appelle l'action hello de mon HelloController
        $controller = new \Twitter\Controller\HelloController();
        $response = $controller->hello();

        // Alors la réponse doit contenir "Bonjour Lior"
        $this->assertEquals("Bonjour Lior", $response->getContent());
        // Et avoir le statut 200 (tout s'est bien passé)
        $this->assertEquals(200, $response->getStatusCode());
        // Et l'entête Content-Type doit contenir "text/html"
        $this->assertEquals("text/html", $response->getHeader('Content-Type'));
    }
}
