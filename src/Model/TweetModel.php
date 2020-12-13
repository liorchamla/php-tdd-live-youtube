<?php

namespace Twitter\Model;

use PDO;
use stdClass;

class TweetModel
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(string $author, string $content): int
    {
        $query = $this->pdo->prepare('INSERT INTO tweet SET content = :content, author = :author, created_at = NOW()');

        $query->execute([
            'content' => $content,
            'author' => $author
        ]);

        // On retourne l'identifiant du tweet nouvellement créé
        return $this->pdo->lastInsertId();
    }

    public function delete(int $id)
    {
        $query = $this->pdo->prepare('DELETE FROM tweet WHERE id = :id');
        $query->execute([
            'id' => $id
        ]);
    }

    public function findById(int $id): ?stdClass
    {
        $query = $this->pdo->prepare('SELECT t.* FROM tweet t WHERE id = :id');
        $query->execute([
            'id' => $id
        ]);

        $tweet = $query->fetch(PDO::FETCH_OBJ);

        if ($tweet === false) {
            return null;
        }

        return $tweet;
    }

    public function findAll(): array
    {
        return $this->pdo
            ->query('SELECT t.* FROM tweet t ORDER BY t.created_at DESC')
            ->fetchAll(PDO::FETCH_OBJ);
    }
}
