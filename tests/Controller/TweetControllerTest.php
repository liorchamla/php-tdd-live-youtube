<?php

use PHPUnit\Framework\TestCase;
use Twitter\Model\TweetModel;

class TweetControllerTest extends TestCase
{
    /** @test */
    public function a_user_can_save_a_tweet()
    {
        // Setup : on va vider la base de données 
        $pdo = new PDO('mysql:host=localhost;dbname=live_test;charset=utf8', 'root', 'root', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $pdo->query("DELETE FROM tweet");

        // Etant donné une requête POST vers /tweet.php
        // Et que les paramètres "content" et "author" sont présents
        $_POST['author'] = 'Lior';
        $_POST['content'] = 'Mon premier tweet';

        $tweetModel = new TweetModel($pdo);

        // Quand mon controller prend la main
        $controller = new \Twitter\Controller\TweetController($tweetModel);
        $response = $controller->saveTweet();

        // Alors je m'attend à être redirigé vers /
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/', $response->getHeader('Location'));

        // Et je m'attend à trouver un tweet dans la base de données
        $result = $pdo->query('SELECT t.* FROM tweet AS t');

        $this->assertEquals(1, $result->rowCount());
        // Et le tweet a le bon author et le bon content
        $data = $result->fetch();
        $this->assertEquals('Lior', $data['author']);
        $this->assertEquals('Mon premier tweet', $data['content']);
    }
}
