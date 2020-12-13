<?php

use PHPUnit\Framework\TestCase;
use Twitter\Model\TweetModel;

class TweetModelTest extends TestCase
{
    protected PDO $pdo;
    protected TweetModel $model;

    protected function setUp(): void
    {
        // Setup : on va vider la base de données 
        $this->pdo = new PDO('mysql:host=localhost;dbname=live_test;charset=utf8', 'root', 'root', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $this->pdo->query("DELETE FROM tweet");
        $this->model = new TweetModel($this->pdo);
    }

    /** @test */
    public function it_can_save_a_tweet()
    {
        // Etant donné un auteur et un contenu
        $author = "Lior";
        $content = "Test de tweet";

        // Quand j'appelle mon model et que je veux sauver un tweet
        $newTweetId = $this->model->save($author, $content);

        // Alors je reçois bien un identifiant
        $this->assertNotNull($newTweetId);
        // Et le tweet correspondant à cet identifiant existe bien
        $tweet = $this->pdo->query('SELECT * FROM tweet WHERE id = ' . $newTweetId)->fetch();

        $this->assertNotFalse($tweet);
        $this->assertEquals($author, $tweet['author']);
        $this->assertEquals($content, $tweet['content']);
    }

    /** @test */
    public function we_can_delete_a_tweet()
    {
        // Etant donné un tweet existant
        $tweetId = $this->model->save("Lior", "Un tweet");

        // Quand je supprime à l'aide du model
        $this->model->delete($tweetId);

        // Alors le tweet n'apparait plus dans la base
        $results = $this->pdo->query("SELECT t.* FROM tweet t WHERE id = $tweetId")->rowCount();
        $this->assertEquals(0, $results);
    }

    /** @test */
    public function we_can_find_a_tweet_with_its_id()
    {
        // Etant donné un tweet existant
        $tweetId = $this->model->save("Lior", "Un tweet");

        // Quand je recherche le tweet avec son id
        $tweet = $this->model->findById($tweetId);

        // Alors le tweet devrait exister
        $this->assertNotNull($tweet);
        // Et contenir les mêmes informations
        $this->assertEquals("Lior", $tweet->author);
        $this->assertEquals("Un tweet", $tweet->content);
    }

    /** @test */
    public function we_cant_find_an_unexisting_tweet()
    {
        // Quand je recherche un tweet inexistant
        $tweet = $this->model->findById(42);

        // Alors le tweet devrait être null
        $this->assertNull($tweet);
    }

    /** @test */
    public function we_can_find_all_tweets()
    {
        // Etant donné un nombre aléatoire de tweets en base de données
        $count = mt_rand(3, 20);
        for ($i = 0; $i < $count; $i++) {
            $this->model->save("Author $i", "Content $i");
        }

        // Quand je demande la liste des tweets
        $tweets = $this->model->findAll();

        // Alors je devrais retrouver autant de tweets que ce qu'il y a dans la base de données
        $this->assertCount($count, $tweets);
    }
}
