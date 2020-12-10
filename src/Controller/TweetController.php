<?php

namespace Twitter\Controller;

use PDO;
use Twitter\Http\Response;
use Twitter\Model\TweetModel;

/**
 * DEUXIEME ENSEIGNEMENT : INVERSION DE CONTRÔLE ET INJECTION DE DEPENDANCES
 * -------------------------------------------------------------------------
 * Au départ, c'était le Controller qui décidait :
 * 1) Quel model il voulait utiliser
 * 2) A quelle base de données il allait se connecter
 * 
 * Ce qui veut dire que pour tester notre Controller, on ne pouvait pas lui spécifier une
 * base de données de tests sur laquelle on peut se lacher et faire carnage.
 * 
 * On a donc décidé que ce n'était plus le controller qui allait décider où se connecter :
 * C'est désormais à nous de créer la connexion comme on le souhaite et de lui passer afin
 * qu'il l'utilise. 
 * 
 * IL N'A PLUS LE CONTRÔLE DE LA CONNEXION, C'EST NOUS QUI L'AVONS ET IL NE FAIT QUE 
 * L'UTILISER
 */

class TweetController
{
    protected PDO $pdo;
    protected TweetModel $model;

    public function __construct(TweetModel $model)
    {
        $this->model = $model;
    }

    public function saveTweet(): Response
    {
        $this->model->save($_POST['author'], $_POST['content']);

        // On retourne une réponse vide, dont le status est 302 (redirection)
        // et dont l'adresse de redirection est "/"
        return new Response('', 302, [
            'Location' => '/'
        ]);
    }
}
