<?php
include 'functions.php';
$pdo = pdo_connect_mysql();
$msg = '';
date_default_timezone_set('Europe/Paris');

// Check if POST data is not empty
if (!empty($_POST)) {
    
    // Set-up the variables that are going to be inserted, we must check if the POST variables exist if not we can default them to blank
    
    
    // Check if POST variable "name" exists, if not default the value to blank, basically the same for all variables
    $nom = isset($_POST['Nom']) ? $_POST['Nom'] : '';
    $liste = isset($_POST['Liste']) ? $_POST['Liste'] : '';
    $dateCrea = isset($_POST['DateCrea']) ? $_POST['DateCrea'] : date('Y-m-d H:i:s');

    // Insert new record into the contacts table
       
        $stmt = $pdo->prepare('INSERT INTO mailing_lists (Nom, Liste, DateCrea) VALUES (:nom, :liste, :datecrea);');
     
        $stmt->bindParam('nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam('liste', $liste, PDO::PARAM_STR);
        $stmt->bindParam('datecrea', $dateCrea, PDO::PARAM_STR);

        $stmt->execute();
     
    // Output message
    $msg = 'Mailing list ajoutée avec succès !';
}
?>

<?=template_header('Création mailing list')?>

<div class="content update">
	<h2>Ajout d'un prescripteur</h2>
    <?php if ($msg): ?>
    <p><?=$msg?>
    <br><br>
    <input type="button" value="Retour" onclick="history.go(-2)"></p>
    <?php endif; ?>
</div>

<?=template_footer()?>