<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
<link rel="stylesheet" type="text/css" href="style.css" />
<title>Sudoku</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="sudoku.js"></script>
</head>
<body>
<div id="chargement" style="position: fixed; width: 90%; height: 100px; bottom: 10%; left: 5%; line-height: 50px; background-color: white; border: 1px solid; visibility: hidden;">Le Sudoku est en cours de résolution, veuillez patienter.<br /><img src="chargement.gif" alt="Chargement..." /></div>
<?php
//Si l'utilisateur à demander l'effacement d'un Sudoku
if(isset($_POST['sauvegardes']) && $_POST['sauvegardes'])
{
	//Ouverture du fichier en lecteur seule puis lecteur du nombre de sudoku sauvegardés
	$sauvegarde = fopen("sauvegarde.txt", "r");
	$nombre = str_replace("\n", "", fgets($sauvegarde));
	
	$supprimer= str_replace("\n", "", $_POST['sauvegardes']);
	
	$compte = 0;
	
	for($i = 0; $i < $nombre; $i++)
	{
		$nomActuel = str_replace("\n", "", fgets($sauvegarde));
		$remplirActuel = str_replace("\n", "", fgets($sauvegarde));
		
		//S'il ne s'agit pas du sudoku à supprimer, il sera remis dans le fichier
		if($remplirActuel !== $supprimer)
		{
			//echo 'POST vaut '.$_POST['sauvegardes'].' et actuel vaut '.$remplirActuel.'<br />';
			
			$nom[$compte] = $nomActuel;
			$remplir[$compte] = $remplirActuel;
			$compte++;
		}
	}
	
	fclose($sauvegarde);
	
	//Ouverture du fichier en écriture avec effacement complet
	$sauvegarde = fopen("sauvegarde.txt", "w+");
	
	//Ecriture du fichier sans le sudoku à effacer
	fputs($sauvegarde, $compte."\n");	
	
	for($i = 0; $i < $compte; $i++)
	{
		
		fputs($sauvegarde, $nom[$i]."\n");
		fputs($sauvegarde, $remplir[$i]."\n");
	}	
	
	fclose($sauvegarde);
}
?>
<div id="sudokuEntier">
<p>Entrez les chiffres du Sudoku à résoudre dans la grille.<br />Vous pouvez utiliser les flèches du clavier pour vous y déplacer.</p>
<noscript><p>Javascript est désactivé,  certaines fonctions ne sont donc pas disponibles. N'hésitez pas à le réactiver, le programme ne risque pas de manger votre ordinateur !</p></noscript>
<form action="sudoku2.php" method="post" name="tableauSudoku">
<table id="tableau">
<?php
//Affichage du Sudoku
$case = 1;
for($i = 0; $i < 9; $i++)
{
	echo '<tr>';
	for($j = 0; $j < 9; $j++)
	{
		$style = "defaut";
		//Bordure plus épaisse pour les bords des pavés
		if($i == 2 || $i == 5)	$style .= " bas ";
		if($j == 2 || $j == 5)	$style .= " droite";
		//Champ de texte pour chaque case
		echo '<td class="'.$style.'"><input type="text" name="case'.$case.'" size="1" maxlength="1" class="remplir" autocomplete="off" 
		onkeydown="bouger(event, '.$case.')" onchange="verifierChiffre('.$case.')" onfocus="prendreFocus('.$case.')" onblur="perdreFocus('.$case.')" /></td>';
		$case ++;
	}
	echo "</tr>\n";
}
?>
</table>
</div>
<div id="explications">Cochez cette case pour afficher les étapes suivies par le programme pour résoudre le Sudoku.<br />Les techniques appliquées, les chiffres trouvés par ces techniques, et l'état du Sudoku à chaque étape
seront alors affichés.<br />Si vous laissez la case décochée, seul le Sudoku résolu sera affiché.</div>
<p><span onmouseover="afficherExplications();" onmouseout="cacherExplications()"><input type="checkbox" name="expliquer" /> Détailler la résolution</span><br />
<input type="checkbox" name="activerAjax" />Activer Ajax</p>
<p><input type="submit" value="Résoudre" onclick="return verifierSudoku()" /> <input type="reset" value="Effacer tous les champs" onclick="return confirmation()" /></p>
</form>

<?php
//Avant de charger le ficier des sauvegardes, on vérifie qu'il existe
if(file_exists("sauvegarde.txt"))
{
	$sauvegarde = fopen("sauvegarde.txt", "r");
	$nombre = str_replace("\n", "", fgets($sauvegarde));
	if($nombre > 0)
	{
		echo '<p>Cliquez sur un Sudoku dans la liste pour le charger</p>';
		echo '
		<form action="sudoku.php" method="post" name="sudokuSauvegardes">
		<p>
		<select name="sauvegardes" size="10" onclick="charger(this.value)">';
		
		for($i = 1; $i <= $nombre; $i++)
		{
			$nom = stripslashes(fgets($sauvegarde));
			$remplir = fgets($sauvegarde);
			echo '<option value="'.$remplir.'">'.$nom.'</option>';
		}
		
		fclose($sauvegarde);
		
		echo '</select>';
		echo '<br />
		<!--<input type="submit" value="Effacer le sudoku sélectionné" onclick="return verifSelectionne()" />-->
		</p>
		</form>';
	}
}
?>
<hr>
<p>Projet Programmation<br />Thibaut Renaux<br />DEUST IOSI 1ère Année</p>
</hr>
</body>
</html>
