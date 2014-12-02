<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN""http://www.w3.org/TR/html4/loose.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
<head>
<title>Sauvegarder le Sudoku</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript">
function AfficherMessage(message)
{
	alert(message);
	window.location.href = "sudoku.php";
}
</script>
</head>
<?php
	//On verifie que l'utilisateur n'accède pas à cette page directement 
	if($_POST['sudoku'])
	{
		//Ouverture du fichier et lecture de la première ligne
		//On vérifie que le fichier existe : si ce n'est pas le cas, il est créé
		if(file_exists('sauvegarde.txt'))
		{
			if(is_writable("sauvegarde.txt"))
			{
				$sauvegarde = fopen("sauvegarde.txt", "r+");
			}
			else
			{
				$message = 'Impossible de charger le fichier sauvegarde.txt. Vérifiez les droits de ce fichier.';
			}
			
		}
		else
		{
			//Le dossier où le fichier va être créé doit être accessible en écriture
			if(is_writable('./'))
			{
				$sauvegarde = fopen("sauvegarde.txt", "x+");
			}
			else
			{
				$message = 'Impossible de créer le fichier sauvegarde.txt. Vérifiez les droits du dossier où sont stockés les scripts, ou créez le fichier manuellement.';
			}
		}
		
		if($sauvegarde)
		{
			$nombre = str_replace("\n", "", fgets($sauvegarde));
			
			//Si le fichier est vide, il s'agit du premier Sudoku saugardé
			if($nombre > 0)
			{
				$nombre++;
			}
			else
			{
				$nombre = 1;
			}
			
			//Retour au debut du fichier
			rewind($sauvegarde);
			
			//Ecriture du nombre de Sudoku dans le fichier
			fputs($sauvegarde, $nombre."\n");
			
			//Placage du pointeur a la fin du fichier
			fseek($sauvegarde, 0, SEEK_END);
			
			//Ecriture dans le fichier du nom et du sudoku
			fputs($sauvegarde, $_POST['nom']."\n");
			fputs($sauvegarde, $_POST['sudoku']."\n");
			
			fclose($sauvegarde);
			
			//Les guillemets font bugger Javascript, on les efface donc
			$nom = str_replace('"', '', $_POST['nom']);
			$message = 'Le sudoku '.$nom.' a été correctement sauvegardé.';
		}

		echo '<body onload="AfficherMessage(\''.$message.'\')">';
	}
	else
	{
		echo '<body>';
	}
?>
</body>
</html>
