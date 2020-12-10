<?php

use PHPUnit\Framework\TestCase;
use Twitter\Model\TweetModel;

class TweetModelTest extends TestCase
{
    /** @test */
    public function it_can_save_a_tweet()
    {
        // Setup : on va vider la base de données 
        $pdo = new PDO('mysql:host=localhost;dbname=live_test;charset=utf8', 'root', 'root', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $pdo->query("DELETE FROM tweet");

        // Etant donné un auteur et un contenu
        $author = "Lior";
        $content = "Test de tweet";

        // Quand j'appelle mon model et que je veux sauver un tweet
        $model = new TweetModel($pdo);
        $newTweetId = $model->save($author, $content);

        // Alors je reçois bien un identifiant
        $this->assertNotNull($newTweetId);
        // Et le tweet correspondant à cet identifiant existe bien
        $tweet = $pdo->query('SELECT * FROM tweet WHERE id = ' . $newTweetId)->fetch();

        $this->assertNotFalse($tweet);
        $this->assertEquals($author, $tweet['author']);
        $this->assertEquals($content, $tweet['content']);
    }
}
