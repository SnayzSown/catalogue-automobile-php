<?php

require 'connexion.php';
$id_marque = isset($_GET['id_marque']) ? (int) $_GET['id_marque'] : null;
$id_modele = isset($_GET['id_modele']) ? (int) $_GET['id_modele'] : null;

// Traitement du formulaire d'ajout de véhicule

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouter_vehicule') {
    $marque_id = (int) $_POST['nouvelle_marque'];
    $nom = ($_POST['nom_modele']);
    $cat = ($_POST['categorie']);
    $annee = (int) $_POST['annee_lancement'];

    // Insertion dans la table Modele (minimum requis pour montrer qu'on modifie la BDD)
    $req_insert = "INSERT INTO modele (nom_modele, categorie, annee_lancement, id_marque) 
                   VALUES ('$nom', '$cat', $annee, $marque_id)";
    ecritureBDD($req_insert);
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue Automobile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</head>

<body>
    <header class="custom-header">
        <h1><a href="index.php"> Mon Catalogue Automobile</a></h1>
        <p>Découvrez les véhicules par marque et modèle</p>
    </header>

    <main class="catalogue-principal">

        <?php

        // ÉTAPE 1 : PAR DÉFAUT -> LES MARQUES
        
        if (empty($id_marque) && empty($id_modele)):
            $req_marques = "SELECT * FROM marques ORDER BY nom_marque ASC";
            $marques = lectureBDD($req_marques);
            ?>
            <details class="zone-ajout-bdd">
                <summary>+ Ajouter un nouveau modèle à la BDD</summary>
                <form action="index.php" method="POST" class="formulaire-sae">
                    <input type="hidden" name="action" value="ajouter_vehicule">

                    <div class="choix-marque">
                        <p>Sélectionnez la marque :</p>
                        <label class="label-radio"><input type="radio" name="nouvelle_marque" value="1" required>
                            BMW</label>
                        <label class="label-radio"><input type="radio" name="nouvelle_marque" value="2" required>
                            Mercedes</label>
                        <label class="label-radio"><input type="radio" name="nouvelle_marque" value="3" required>
                            Volkswagen</label>
                        <label class="label-radio"><input type="radio" name="nouvelle_marque" value="4" required>
                            Audi</label>
                    </div>

                    <input type="text" name="nom_modele" placeholder="Nom du modèle (ex: 118d 2.0)" required>
                    <input type="text" name="categorie" placeholder="Catégorie (ex: Serie 1,... )" required>
                    <select name="annee_lancement" required>
                        <option value="" disabled selected>Année de lancement</option>
                        <?php
                        // Boucle PHP pour générer les années (de la plus récente à la plus ancienne)
                        for ($annee = 2026; $annee >= 2000; $annee--) {
                            echo "<option value='$annee'>$annee</option>";
                        }
                        ?>
                    </select>

                    <button type="submit" class="btn-voir">Ajouter au catalogue</button>
                </form>
            </details>
            <div class="grille-marques">
                <?php foreach ($marques as $m): ?>
                    <a href="index.php?id_marque=<?php echo $m['id_marque']; ?>" class="carte-marque">
                        <img src="<?php echo ($m['logo']); ?>" alt="Logo <?php echo ($m['nom_marque']); ?>">
                        <h3><?php echo ($m['nom_marque']); ?></h3>
                    </a>
                <?php endforeach; ?>
            </div>

            <?php

            // ÉTAPE 2 : Si on a cliqué sur une marque alors afficher les modèles de cette marque
        
        elseif (!empty($id_marque) && empty($id_modele)):

            // On fait un LEFT JOIN avec la table voiture pour récupérer l'image de présentation !
        
            $req_modeles = "SELECT modele.*,
                       (SELECT lien_image FROM images_voiture WHERE images_voiture.id_voiture = voiture.id_voiture LIMIT 1) AS image
                FROM modele
                LEFT JOIN voiture ON modele.id_modele = voiture.id_modele
                WHERE modele.id_marque = $id_marque
                ORDER BY modele.nom_modele ASC";

            $modeles = lectureBDD($req_modeles);
            ?>
            <div class="page-liste">

                <div class="section-titre">

                    <a href="index.php" class="btn-retour">← Retour aux marques</a>
                    <h2>Modèles disponibles</h2>

                    <div class="zoneFiltres">
                        <span style="font-weight: bold;">Filtrer par année :</span>

                        <input type="checkbox" name="FiltreAvant2010" id="Filtre1">
                        <label for="Filtre1">Avant 2010</label>

                        <input type="checkbox" name="FiltreApres2010" id="Filtre2">
                        <label for="Filtre2">À partir de 2010</label>
                    </div>
                </div>
                <div class="grille-modeles-photos">

                    <?php if (!empty($modeles)): ?>

                        <?php foreach ($modeles as $m): ?>
                            <?php
                            // On lit l'année dans la BDD pour générer la classe CSS correspondante
                            $classe_annee = (intval($m['annee_lancement']) >= 2010) ? 'apres-2010' : 'avant-2010';
                            ?>
                            <a href="index.php?id_modele=<?php echo $m['id_modele']; ?>"
                                class="carte-modele-photo <?php echo $classe_annee; ?>">
                                <?php if (!empty($m['image'])): ?>
                                    <img src="<?php echo ($m['image']); ?>" alt="Photo <?php echo ($m['nom_modele']); ?>">
                                <?php else: ?>
                                    <div class="pas-de-photo">Pas de photo disponible</div>
                                <?php endif; ?>

                                <div class="carte-modele-corps">
                                    <h3><?php echo ($m['nom_modele']); ?></h3>
                                    <p>Catégorie : <?php echo ($m['categorie']); ?></p>
                                    <span class="badge-annee">Année : <?php echo ($m['annee_lancement']); ?></span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>Aucun modèle enregistré pour cette marque.</p>
                    <?php endif; ?>
                </div>
            </div>

            <?php

            // ÉTAPE 3 : Si on a cliqué sur un modèle alors afficher les détails de ce modèle
        elseif (!empty($id_modele)):

            $req_voiture = "SELECT voiture.*, modele.nom_modele, marques.nom_marque, marques.id_marque
                            FROM voiture
                            INNER JOIN modele ON voiture.id_modele = modele.id_modele
                            INNER JOIN marques ON modele.id_marque = marques.id_marque
                            WHERE voiture.id_modele = $id_modele";

            $resultat = lectureBDD($req_voiture);

            if (!empty($resultat) && isset($resultat[0]['id_voiture'])):

                $v = $resultat[0];

                $id_v = $v['id_voiture'];
                // Aller chercher les images secondaires
        
                $req_images = "SELECT lien_image FROM images_voiture WHERE id_voiture = $id_v";

                $galerie = lectureBDD($req_images);

                ?>
                <div class="page-detail">

                    <a href="index.php?id_marque=<?php echo $v['id_marque']; ?>" class="btn-retour">← Retour aux modèles</a>
                    <div class="detail-container">
                        <div id="carouselVoiture" class="carousel slide" data-bs-ride="carousel">

                            <div class="carousel-inner rounded shadow bg-dark">

                                <?php if (!empty($galerie)): ?>

                                    <?php foreach ($galerie as $index => $img): ?>

                                        <div class="carousel-item <?php echo ($index === 0) ? 'active' : ''; ?>">
                                            <img src="<?php echo ($img['lien_image']); ?>" class="d-block img-carrousel mx-auto"
                                                alt="Photo de la voiture">
                                        </div>
                                    <?php endforeach; ?>

                                <?php else: ?>

                                    <div class="carousel-item active">
                                        <div class="pas-d-image">Aucune photo disponible pour ce véhicule</div>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselVoiture"
                                data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Précédent</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselVoiture"
                                data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Suivant</span>
                            </button>
                        </div>

                        <div class="detail-infos">
                            <h2><?php echo ($v['nom_marque'] . ' ' . $v['nom_modele']); ?></h2>
                            <p class="prix-badge"><?php echo $v['prix']; ?> €</p>
                            <ul class="details-spec">
                                <li><strong>Énergie :</strong> <?php echo $v['energie']; ?></li>
                                <li><strong>Puissance :</strong> <?php echo $v['puissance_ch']; ?> ch</li>
                                <li><strong>Couleur :</strong> <?php echo $v['couleur']; ?></li>
                            </ul>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <div class="page-detail">
                    <a href="javascript:history.back()" class="btn-retour">← Retour en arrière</a>
                    <p class="erreur-statut">Désolé, les spécifications techniques et les images de ce modèle n'ont pas encore
                        été enregistrées.</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>
    <script src="js/main.js"></script>
</body>

</html>