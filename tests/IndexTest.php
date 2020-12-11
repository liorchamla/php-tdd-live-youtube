<?php

use PHPUnit\Framework\TestCase;
use Twitter\Controller\HelloController;

class IndexTest extends TestCase
{
    protected HelloController $controller;

    protected function setUp(): void
    {
        $this->controller = new HelloController;
    }

    public function test_homepage_says_hello()
    {
        // Etant donné une requête HTTP avec un paramètre name qui vaut Lior
        $_GET['name'] = "Lior";

        // Quand j'appelle l'action hello de mon HelloController
        $response = $this->controller->hello();

        // Alors la réponse doit contenir "Bonjour Lior"
        $this->assertEquals("Bonjour Lior", $response->getContent());
        // Et avoir le statut 200 (tout s'est bien passé)
        $this->assertEquals(200, $response->getStatusCode());
        // Et l'entête Content-Type doit contenir "text/html"
        $this->assertEquals("text/html", $response->getHeader('Content-Type'));
    }

    /** @test */
    public function test_it_works_even_if_there_is_no_name_in_GET()
    {
        // Etant donné qu'il n'y a rien dans le GET
        $_GET = [];

        // Quand j'appelle mon controller
        $response = $this->controller->hello();

        // Alors la response devrait contenir "Bonjour tout le monde"
        $this->assertEquals("Bonjour tout le monde", $response->getContent());
    }
}
