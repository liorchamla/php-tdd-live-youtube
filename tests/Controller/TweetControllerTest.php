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

    /** @test */
    public function we_can_delete_a_tweet_with_its_id()
    {
        // Etant donné qu'un tweet existe dans la base de données
        $tweetId = $this->tweetModel->save("Lior", "Un tweet de test");

        // Et que j'ai un paramètre ID dans la requête HTTP
        $request = new Request([
            'id' => $tweetId
        ]);

        // Quand j'appelle le controller
        $response = $this->controller->deleteTweet($request);

        // Alors la réponse doit être au statut 302 (Redirection)
        $this->assertEquals(302, $response->getStatusCode());
        // Et la location dans les entêtes doit être "/"
        $this->assertEquals('/', $response->getHeader('Location'));
        // Et le tweet ne doit plus exister
        $results = $this->pdo->query("SELECT t.* FROM tweet t WHERE id = $tweetId");
        $this->assertEquals(0, $results->rowCount());
    }

    /** @test */
    public function we_cant_delete_a_tweet_with_no_id()
    {
        // Etant donné qu'on ne donne rien dans la requête
        $request = new Request(); // Donc pas d'identifiant de tweet

        // Quand j'appelle le controller
        $response = $this->controller->deleteTweet($request);

        // Alors la réponse devrait avoir un statut 400
        $this->assertEquals(400, $response->getStatusCode());
        // Et le contenu devrait être "Vous devez spécifier l'identifiant du tweet à supprimer"
        $this->assertEquals("Vous devez spécifier l'identifiant du tweet à supprimer", $response->getContent());
    }

    /** @test */
    public function we_can_see_a_tweet_with_its_id()
    {
        // Etant donné un tweet en base de données
        $tweetId = $this->tweetModel->save("Lior", "Un tweet");

        // Et un paramètre id dans la request
        $request = new Request([
            'id' => $tweetId
        ]);

        // Quand j'appelle mon controller pour afficher un tweet
        $response = $this->controller->displayTweet($request);

        // Alors le statut de la response doit être 200 (OK)
        $this->assertEquals(200, $response->getStatusCode());
        // Et le contenu de la response doit contenir l'auteur du tweet et son contenu
        $this->assertStringContainsString('Lior', $response->getContent());
        $this->assertStringContainsString('Un tweet', $response->getContent());

        // On renouvelle le test avec un autre tweet
        $tweetId2 = $this->tweetModel->save("Magali", "Un autre tweet");
        $response2 = $this->controller->displayTweet(new Request(['id' => $tweetId2]));

        // Alors le statut de la response doit être 200 (OK)
        $this->assertEquals(200, $response2->getStatusCode());
        // Et le contenu de la response doit contenir l'auteur du tweet et son contenu
        $this->assertStringContainsString('Magali', $response2->getContent());
        $this->assertStringContainsString('Un autre tweet', $response2->getContent());
    }

    /** @test */
    public function we_cant_see_an_unexisting_tweet()
    {
        // Etant donné une requête pour un tweet inexistant
        $request = new Request(['id' => 42]);

        // Quand j'appelle le controller pour afficher ce tweet
        $response = $this->controller->displayTweet($request);

        // Alors la response devrait avoir le statut 404
        $this->assertEquals(404, $response->getStatusCode());
        // Et le message devrait être "Aucun tweet ne possède l'identifiant 42"
        $this->assertEquals("Aucun tweet ne possède l'identifiant 42", $response->getContent());
    }

    /** @test */
    public function we_can_see_all_tweets()
    {
        // Etant donné un nombre aléatoire de tweets
        $count = mt_rand(3, 20);
        for ($i = 0; $i < $count; $i++) {
            $this->tweetModel->save("Author $i", "Content $i");
        }

        // Quand j'appelle mon controller pour afficher la liste
        $response = $this->controller->displayAllTweets();

        // Alors la response devrait avoir le statut 200
        $this->assertEquals(200, $response->getStatusCode());
        // Et on devrait retrouver les auteurs et contenus des tweets dans la response
        for ($i = 0; $i < $count; $i++) {
            $this->assertStringContainsString("Author $i", $response->getContent());
            $this->assertStringContainsString("Content $i", $response->getContent());
        }
    }
}
