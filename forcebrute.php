<?php

//Fonction qui vérifie qu'une case peut-être remplie avec le chiffre donné
//Retourne 1 si le chiffre peut-être placé, 0 sinon
function Valider($copie, $ligneCase, $colonneCase, $chiffre)
{
	$valide = 1;
	
	//Analyse de la ligne
	for($colonne = 1; $colonne <= 9 && $valide == 1; $colonne++)
	{
		if($copie[$ligneCase][$colonne] == $chiffre)
		{
			$valide = 0;
		}
	}
			
	//Puis la colonne
	for($ligne = 1; $ligne <= 9 && $valide == 1; $ligne++)
	{
		if($copie[$ligne][$colonneCase] == $chiffre)
		{
			$valide = 0;;
		}
	}
			
	//Puis le pavé
	//Determination du pavé à parcouvrir
	$ligneDe = Pave($ligneCase);
	$colonneDe = Pave($colonneCase);
	
	$ligneA = $ligneDe + 2;
	$colonneA = $colonneDe + 2;
	
	//Parcours du pavé déterminé
	for($ligne = $ligneDe; $ligne <= $ligneA && $valide == 1; $ligne++)
	{
		for($colonne = $colonneDe; $colonne <= $colonneA && $valide == 1; $colonne++)
		{
			if($copie[$ligne][$colonne] == $chiffre)
			{
				$valide = 0;
			}
		}
	}
	
	return $valide;
}


function ForceBrute($sudoku)
{
	//Augmentation de la durée du timeout à 10 minutes (au lieu de 30 secondes) : certains Sudoku difficiles(très peu heureusement) peuvent mettre du temps à être résolu selon la puissance du serveur.
	//C'est aussi le temps nécessaire pour savoir si un Sudoku n'est pas valide, quand il y a vraiment énormément de combinaisons possibles
	set_time_limit(600);

	//Copie du Sudoku dans une nouvelle variable : on va faire un tas de modifcations qui seront pour la plupart incorrectes, on ne va pas les faire sur l'original
	for($ligne = 1; $ligne <= 9; $ligne++)
	{
		for($colonne = 1; $colonne <= 9; $colonne++)
		{
			$copie[$ligne][$colonne] = $sudoku[$ligne][$colonne]->chiffre;
			$copieChiffresPossibles[$ligne][$colonne] = $sudoku[$ligne][$colonne]->chiffresPossibles;
		}
	}
	
	$casesRemplies = 0;
	$depart = 1;
	$ligne = 1;
	$colonne = 1;
	
	//Tant que toutes les cases ne sont pas remplies
	//Si $ligne vaut 0, on est remonté à la première case du Sudoku. Dans ce cas, on s'arrête là : on a essayé toutes les combinaisons et il n'y a aucune solution
	while($casesRemplies < 81 && $ligne > 0)
	{
		$valide = 1;
		$chiffre = $depart;
		
		//Tant qu'on trouve des chiffres à mettre
		while($valide == 1 && $casesRemplies < 81)
		{
			//Si la case est vide
			if($copie[$ligne][$colonne] == -1)
			{
				$valide = 0;
				//On essaye de mettre un chiffre jusqu'à en trouver un valide (ou ne pas en trouver du tout)
				while($chiffre <= 9 && $valide == 0)
				{
					$valide = Valider($copie, $ligne, $colonne, $chiffre);
					
					//Si le chiffre peut être mis, on le fait et on passe à la case suivante.
					//Sinon, on essaye le chiffre suivant
					if($valide == 1 && $copieChiffresPossibles[$ligne][$colonne][$chiffre] == 1) 
					{
						$copie[$ligne][$colonne] = $chiffre;
						$casesRemplies++;
						$canard = $copieChiffresPossibles[$ligne][$colonne][$chiffre];
						
						$chiffre = 1;
						
						if($colonne == 9)
						{
							$ligne++;
							$colonne = 1;
						}
						else
						{
							$colonne++;
						}
						
					}
					else
					{
						$chiffre++;
					}
				}
			}
			//Si elle a déjà un chiffre on passe à la suivante
			else
			{
				
				$valide = 1;
				if($colonne == 9)
				{
					$ligne++;
					$colonne = 1;
				}
				else
				{
					$colonne++;
				}
				
				$casesRemplies++;
				
				
			}
		}
		
		if($casesRemplies < 81)
		{
			//La case n'a plus de solution : on revient en arrière pour corriger l'erreur
			
			$copie[$ligne][$colonne] = -1;
			
			//Tant que la case sur le Sudoku original n'est pas vide, on revient en arrière jusqu'à trouver une case vide
			do
			{
				if($colonne == 1)
				{
					$ligne--;
					$colonne = 9;
				}
				else
				{
					$colonne--;
				}
				$casesRemplies--;
				
			}while($sudoku[$ligne][$colonne]->chiffre != -1 && $ligne > 0);
			
			
			//On prend la valeur de cette case comme point de départ pour trouver un nouveau chiffre
			$depart = $copie[$ligne][$colonne]+1;
			$copie[$ligne][$colonne] = -1;
		}
	}
	
	//Si on est arrivé ici, le Sudoku a été résolu (ou est impossible). On copie les données de $copie dans le Sudoku réel
	for($ligne = 1; $ligne <= 9; $ligne++)
	{
		for($colonne = 1; $colonne <= 9; $colonne++)
		{
			$sudoku[$ligne][$colonne]->chiffre = $copie[$ligne][$colonne];
		}
	}
}

?>
