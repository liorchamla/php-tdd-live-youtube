<?php


use Twitter\Controller\HelloController;

/**
 * PREMIER ENSEIGNEMENT : SEPARATION DES PREOCCUPATIONS
 * ----------------------------------------------------
 * Si on veut pouvoir tester facilement les choses, on ne mélange pas :
 * 1) Le code qui va examiner la requête et créer la réponse
 * 2) Le code qui envoie la réponse au navigateur
 * 
 * En effet, si on envoie réellement la réponse au navigateur alors qu'on utilise
 * PHPUnit en mode console, on risque d'avoir des soucis.
 * 
 * Par ailleurs, tester les choses envoyées avec la fonction header() de PHP n'est pas
 * facile avec PHPUnit même si on peut le faire en utilisant des fonctionnalités de 
 * l'extension XDebug (mais donc ça demande d'installer une extension de PHP etc)
 * 
 * Aussi, ça nécessite de lancer les tests de PHPUnit dans un  processus séparé sinon ça risque
 * de faire planter les choses etc
 * 
 * Conclusion : mieux vaut avoir un objet qui représente la response et qui permet ensuite 
 * de tester que la réponse est bien conforme à ce qu'on attend 
 */

require_once __DIR__ . '/vendor/autoload.php';

// Appel du controller et récupération de la réponse
// C'est CA qu'on va tester dans PHPUnit
$controller = new HelloController;
$response = $controller->hello();

// Envoi de la réponse au navigateur
// Ca on ne le teste pas dans PHPUnit car complexe (nécessité de XDebug + process séparé)
$response->send();
