<?php
include 'functions.php';
// Connect to MySQL database
$pdo = pdo_connect_mysql();

// Get the page via GET request (URL param: page), if non exists default the page to 1
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$ui = isset($_POST['choix_civilite']) ? $_POST['choix_civilite'] : '';

// Number of records to show on each page
$records_per_page = 20;  

// Prepare the SQL statement and get records from our prescripteurs table, LIMIT will determine the page
/*$stmt = $pdo->prepare('SELECT * FROM Candidats WHERE Civilite=:choix_civilite LIMIT :current_page, :record_per_page;');
$req = $pdo->prepare('SELECT COUNT(*) FROM Candidats WHERE Civilite=:choix_civilite');
$req2 = $pdo->prepare('SELECT COUNT(*) FROM Candidats');
$stmt->bindValue(':current_page', ($page-1)*$records_per_page, PDO::PARAM_INT);
$stmt->bindValue(':record_per_page', $records_per_page, PDO::PARAM_INT);
$stmt->bindParam('choix_civilite', $ui, PDO::PARAM_STR);
$req->bindParam('choix_civilite', $ui, PDO::PARAM_STR);
$stmt->execute();
$req->execute();
$req2->execute();
$pk=$req->fetchColumn();
$nbr_total=$req2->fetchColumn();
$pourcentage=($pk*100)/($nbr_total);*/


// Fetch the records so we can display them in our template.
//$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the total number of prescripteurs, this is so we can determine whether there should be a next and previous button
$nbr_total = $pdo->query('SELECT COUNT(*) FROM Candidats')->fetchColumn();


// Check if POST data is not empty
if (!empty($_POST)) {

    // Post data not empty insert a new record

    // "Filters" part

    $initialQuery = "SELECT DISTINCT * FROM Candidats WHERE ";
    $conditions = "";
    $compteur = "SELECT COUNT(*) FROM Candidats WHERE";


    foreach($_POST as $key => $value) {
        $search_criteria="";
        if($key!="CodePostal") {
            $search_criteria="'%".$value."%'";
        } else {
            $search_criteria="'".$value."%'";
        }

        if (strlen($conditions) == 0) {
            $date_debut='';
            if($key!="DateDebut" || $key!="DateFin") {
                $conditions = $conditions." (".$key." LIKE ".$search_criteria.") ";
            }
            else {
                $conditions = $conditions." (".$key." BETWEEN '".$value."') ";
            }
        } 
        else {
            if ($value != '' && $key !="DateDebut" && $key !="DateFin") {
                $conditions = $conditions." AND (".$key." LIKE ".$search_criteria.") ";
            }    
        }
    }
    if(empty($_POST["DateDebut"]) || empty($_POST["DateFin"])){

    }
    else {
        $conditions = $conditions." AND (DateEntree BETWEEN '".$_POST["DateDebut"]."'"." AND "."'".$_POST["DateFin"]."') ";
    }
    // rajouter date "de" et "jusqu'??" avec input type date
    // supprimer le datedebut et datefin du $condition (pr??sent dans le $post) en jouant sur les condition (l56 ?? ligne60 V??RIFIER QUE le $key= date debut ou fin et faire un code par rapport a ca)
    // utiliser https://www.tutorialspoint.com/how-to-convert-a-string-to-date-in-mysql#:~:text=The%20following%20is%20the%20syntax,Let%20us%20now%20implement%20it. pour faire le between

    $finalQuery = $pdo->prepare($initialQuery.$conditions);
    //print_r($finalQuery);
    $finalQuery->execute();
    $data = $finalQuery->fetchAll(PDO::FETCH_ASSOC);
    $test = $pdo->prepare($compteur.$conditions." LIMIT ".($page-1)*$records_per_page.", ".$records_per_page);
    $test->execute();
    $compteur_requete = $test->fetchColumn();
}else{

    $initialQuery = $pdo->prepare("SELECT * FROM Candidats LIMIT ".($page-1)*$records_per_page.", ".$records_per_page);
    $initialQuery->execute();
    $data = $initialQuery->fetchAll(PDO::FETCH_ASSOC); 
}
?>

<?=template_header('Candidats');

if(empty($_POST)){
    echo("il y a au total ".$nbr_total." ??l??ment(s) dans votre base de donn??es");
}
else{
    echo("il y a ".$compteur_requete." ??l??ments correspondants ?? votre requ??te");
}
?>

<div class="content read">
	<h2>Candidats</h2>
    <form method="post" action="read_stat.php">
        <label for="Civilite">Civilit??</label>
        <input type="text" name="Civilite">
        </br>
        <label for="Entite">Entit?? :</label> 
        <input type="text" name="Entite">
        </br>
        <label for="TypeOrigine">Type d'origine</label> 
        <input type="text" name="TypeOrigine">
        </br> 
        <label for="Nom">Nom :</label> 
        <input type="text" name="Nom">
        </br>
        <label for="Prenom">Pr??nom :</label> 
        <input type="text" name="Prenom">
        </br>
        <label for="Origine">Origine :</label> 
        <input type="text" name="Origine">
        </br> 
        <label for="CodePostal">Code postal :</label> 
        <input type="text" name="CodePostal">
        </br>
        <label for="Age">Age :</label> 
        <input type="text" name="Age">
        </br>
        <label for="TypeContratSouhaite">Type de contrat souhait?? :</label> 
        <input type="text" name="TypeContratSouhaite">
        </br>
        <label for="RecruteurAyantAjoute">Recruteur ayant ajout?? le candidat :</label> 
        <input type="text" name="RecruteurAyantAjoute">
        </br>
        <label for="Categorie">Cat??gorie :</label> 
        <input type="text" name="Categorie">
        </br>
        <label for="DernierNiveauEtude">Dernier niveau d'??tude du candidat :</label> 
        <input type="text" name="DernierNiveauEtude">
        </br>
        <label for="SecteursActivitesSouhaites">Secteurs d'activit??s souhait??s :</label> 
        <input type="text" name="SecteursActivitesSouhaites">
        </br>
        <label for="PosteSouhaite">Poste souhait?? :</label> 
        <input type="text" name="PosteSouhaite">
        </br>
        <label for="Experience">Exp??rience (en mois) :</label> 
        <input type="text" name="Experience">
        </br>
        <label for="SituationActuelle">Situation actuelle :</label> 
        <input type="text" name="SituationActuelle">
        </br>
        <label for="AvancementRecherche">Avancement de la recherche :</label> 
        <input type="text" name="AvancementRecherche">
        </br>
        <label for="Tags">Tags :</label> 
        <input type="text" name="Tags">
        </br>
        <label for="EtatAvancement">Etat d'avancement :</label> 
        <input type="text" name="EtatAvancement">
        </br>
        <label for="DateAnonymisee">Date anonymis??e :</label> 
        <input type="text" name="DateAnonymisee">
        </br>
        <label for="NombreEmailsEnvoyes">Nombre d'emails envoy??s :</label> 
        <input type="text" name="NombreEmailsEnvoyes">
        </br>
        <label for="NombreCommentairesSaisis">Nombre de commentaires saisis :</label> 
        <input type="text" name="NombreCommentairesSaisis">
        </br>
        <label for="DateDebut">Saisissez une date de d??but :</label> 
        <input type="date" name="DateDebut">
        </br>
        <label for="DateFin">Saisissez une date de fin :</label> 
        <input type="date" name="DateFin">
        </br>
        <button type="submit">Valider</button>
    </form>
	<table>
        <thead>
            <tr>
        </br>

                <td align=center>Civilit??</td>
                <td align=center>Nom</td>
                <td align=center>Pr??nom</td>
                <td align=center>Age</td>
                <td align=center>Code postal</td>
                <td align=center>Date d'entr??e</td>
                <td align=center>Dernie??re date de modification</td> 
                <td align=center>Recruteur l'ayant ajout??</td>   
                <td align=center>Entit??</td>            
                <td align=center>Type d'origine</td>
                <td align=center>Cat??gorie</td>
                <td align=center>Origine</td>                
                <td align=center>Dernier niveau d'??tude</td>
                <td align=center>Secteurs d'activit??s souhait??s</td>
                <td align=center>Poste souhait??</td>
                <td align=center>Exp??rience (en mois)</td>
                <td align=center>Type de contrat souhat??</td>
                <td align=center>Situation actuelle</td>
                <td align=center>Avancement de la recherche</td>
                <td align=center>Tags</td>
                <td align=center>Etat d'avancement</td>
                <td align=center>Date anonymis??e</td>
                <td align=center>Nbr d'emails envoy??s</td>
                <td align=center>Nbr de commentaires saisis</td>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $data): ?>
            <tr>
                <td align=center><?=$data['Civilite']?></td>
                <td align=center><?=$data['Nom']?></td>
                <td align=center><?=$data['Prenom']?></td>
                <td align=center><?=$data['Age']?></td>
                <td align=center><?=$data['CodePostal']?></td>
                <td align=center><?=$data['DateEntree']?></td>
                <td align=center><?=$data['DerniereDateModif']?></td> 
                <td align=center><?=$data['RecruteurAyantAjoute']?></td> 
                <td align=center><?=$data['Entite']?></td>              
                <td align=center><?=$data['TypeOrigine']?></td>
                <td align=center><?=$data['Categorie']?></td>
                <td align=center><?=$data['Origine']?></td>                
                <td align=center><?=$data['DernierNiveauEtude']?></td>
                <td align=center><?=$data['SecteursActivitesSouhaites']?></td>
                <td align=center><?=$data['PosteSouhaite']?></td>
                <td align=center><?=$data['Experience']?></td>
                <td align=center><?=$data['TypeContratSouhaite']?></td>
                <td align=center><?=$data['SituationActuelle']?></td>
                <td align=center><?=$data['AvancementRecherche']?></td>
                <td align=center><?=$data['Tags']?></td>
                <td align=center><?=$data['EtatAvancement']?></td>
                <td align=center><?=$data['DateAnonymisee']?></td>
                <td align=center><?=$data['NombreEmailsEnvoyes']?></td>
                <td align=center><?=$data['NombreCommentairesSaisis']?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
	<div class="pagination">
		<?php if ($page > 1): ?>
		<a href="read_stat.php?page=<?=$page-1?>"><i class="fas fa-angle-double-left fa-sm"></i></a>
		<?php endif; ?>
		<?php if ($page*$records_per_page < $nbr_total): ?>
		<a href="read_stat.php?page=<?=$page+1?>"><i class="fas fa-angle-double-right fa-sm"></i></a>
		<?php endif; ?>
	</div>
</div>

<?=template_footer()?>
