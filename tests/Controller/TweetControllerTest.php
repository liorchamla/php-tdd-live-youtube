<?php

use PHPUnit\Framework\TestCase;
use Twitter\Controller\TweetController;
use Twitter\Http\Request;
use Twitter\Model\TweetModel;
use Twitter\Validation\RequestValidator;

class TweetControllerTest extends TestCase
{
    protected PDO $pdo;
    protected TweetModel $tweetModel;
    protected TweetController $controller;

    protected function setUp(): void
    {
        $this->pdo = new PDO('mysql:host=localhost;dbname=live_test;charset=utf8', 'root', 'root', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $this->pdo->query("DELETE FROM tweet");

        $this->tweetModel = new TweetModel($this->pdo);

        $this->controller = new TweetController(
            $this->tweetModel,
            new RequestValidator
        );
    }

    /** @test */
    public function a_user_can_save_a_tweet()
    {
        // Etant donné une requête POST vers /tweet.php
        // Et que les paramètres "content" et "author" sont présents
        $request = new Request([
            'author' => 'Lior',
            'content' => 'Mon premier tweet'
        ]);

        // Quand mon controller prend la main
        $response = $this->controller->saveTweet($request);

        // Alors je m'attend à être redirigé vers /
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/', $response->getHeader('Location'));

        // Et je m'attend à trouver un tweet dans la base de données
        $result = $this->pdo->query('SELECT t.* FROM tweet AS t');

        $this->assertEquals(1, $result->rowCount());
        // Et le tweet a le bon author et le bon content
        $data = $result->fetch();
        $this->assertEquals('Lior', $data['author']);
        $this->assertEquals('Mon premier tweet', $data['content']);
    }

    /** 
     * @test 
     * @dataProvider missingFieldsProvider
     */
    public function it_cant_save_a_tweet_if_fields_are_missing($postData, $errorMessage)
    {
        // Etant donné qu'on a bien un content dans le $_POST
        // mais pas d'author
        $request = new Request($postData);

        // Quand j'appelle mon TweetController
        $response = $this->controller->saveTweet($request);

        // Alors, la réponse devrait avoir un statut 400
        $this->assertEquals(400, $response->getStatusCode());
        // Et le contenu de la réponse devrait être "Le champ author est manquant"
        $this->assertEquals($errorMessage, $response->getContent());
    }

    public function missingFieldsProvider()
    {
        return [
            [
                ['content' => "Test de tweet"],
                "Le champ author est manquant"
            ], // Représente les paramètres à passer à un test
            [
                ['author' => "Lior"],
                "Le champ content est manquant"
            ],
            [
                [],
                "Les champs author, content sont manquants"
            ]
        ];
    }
}
