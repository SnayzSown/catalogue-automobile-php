<?php
/*Initialise la connexion à la base de données*/
function connexionBDD()
{
    $database = null;
    try {
        $options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );
        $database = new PDO('mysql:host=localhost;dbname=sae-203', 'root', '', $options);
    }
    catch(Exception $err) {
        die('Erreur connexion MySQL : ' . $err->getMessage());
    }
    return $database;
}

function lectureBDD($requete)
{
    $bdd = connexionBDD();
    $reponse = $bdd->query($requete);
    $tableau = $reponse->fetchAll(PDO::FETCH_ASSOC);
    $bdd = null; 
    return $tableau;
}

function ecritureBDD($requete)
{
    $bdd = connexionBDD();
    $nb = $bdd->exec($requete);
    $bdd = null; 
    return $nb;
}
?>