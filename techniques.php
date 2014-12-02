<?php
//TECHNIQUES DE RESOLUTION

//Resolution par la technique du seul candidat
function SeulCandidat($sudoku, $expliquer)
{	
	$casesRemplies = 0;
	//On reconstruit la liste des candidats possibles pour chaque case
	VerifierEliminationCasesVides($sudoku, $expliquer);
	//Puis on tente de remplir les cases si c'est possible
	for($i = 1; $i <= 9; $i++)
	{
		for($j = 1; $j <= 9; $j++)
		{
			$casesRemplies += $sudoku[$i][$j]->Remplir($expliquer);
		}	
	}
	
	return $casesRemplies;
}

//Candidat unique
function CandidatUnique($sudoku, $expliquer)
{	
	$casesRemplies = 0;
	
	for($chiffre = 1; $chiffre <= 9; $chiffre++)
	{
		
		//Parcours du sudoku ligne par ligne
		for($i = 1; $i <= 9; $i++)
		{
			$nbChiffresPossibles = RemplirTableau(0);
			for($j = 1; $j <= 9; $j++)
			{
				//Si la case est vide
				if($sudoku[$i][$j]->chiffre == -1)
				{
					//On ajoute les candidats possibles dans un tableau
						if($sudoku[$i][$j]->chiffresPossibles[$chiffre] == 1)
						{
							$nbChiffresPossibles[$chiffre]++;
							$colonneCandidatUnique[$chiffre] = $j;
						}
				}
			}
			
			//On a terminé de parcourir la ligne, on analyse le tableau pour savoir s'il y a des candidats uniques
			if($nbChiffresPossibles[$chiffre] == 1)
			{
				//La ligne contient une case qui est la seule à pouvoir contenir le chiffre : elle le contient donc.
				$colonne = $colonneCandidatUnique[$chiffre];
				$sudoku[$i][$colonne]->chiffre = $chiffre;
				$sudoku[$i][$colonne]->chiffresPossibles = RemplirTableau(-1);
				EliminerChiffre($chiffre, $i, $colonne);
				$casesRemplies++;
				if($expliquer == 1) echo 'Chiffre <strong>'.$chiffre.'</strong> placé dans la case <strong>L'.$i.' C'.$colonne.'</strong>, seul '.$chiffre.' dans la ligne<br />';
			}
		}
		
		//Parcours du sudoku colonne par colonne
		for($j = 1; $j <= 9; $j++)
		{
			$nbChiffresPossibles = RemplirTableau(0);
			for($i = 1; $i <= 9; $i++)
			{
				//Si la case est vide
				if($sudoku[$i][$j]->chiffre == -1)
				{
					//On ajoute les candidats possibles dans un tableau
						if($sudoku[$i][$j]->chiffresPossibles[$chiffre] == 1)
						{
							$nbChiffresPossibles[$chiffre]++;
							$ligneCandidatUnique[$chiffre] = $i;
						}
				}
			}
			
			
			//On a terminé de parcourir la colonne, on analyse le tableau pour savoir s'il y a des candidats uniques
			if($nbChiffresPossibles[$chiffre] == 1)
			{
				//La colonne contient une case qui est la seule à pouvoir contenir le chiffre : elle le contient donc.
				$ligne = $ligneCandidatUnique[$chiffre];
				$sudoku[$ligne][$j]->chiffre = $chiffre;
				$sudoku[$ligne][$j]->chiffresPossibles = RemplirTableau(-1);
				EliminerChiffre($chiffre, $ligne, $j);
				$casesRemplies++;
				if($expliquer == 1) echo 'Chiffre <strong>'.$chiffre.'</strong> dans la case placé <strong>L'.$ligne.' C'.$j.'</strong>, seul '.$chiffre.' dans la colonne<br />';
			}
		}
		
		//Parcours du sudoku pavé par pavé
		for($ligne = 1; $ligne <= 7; $ligne += 3)
		{
			for($colonne = 1; $colonne <= 7; $colonne += 3)
			{
				$nbChiffresPossibles = RemplirTableau(0);
				$ligneJusque = $ligne+2;
				$colonneJusque = $colonne+2;
							
				for($lignePave = $ligne; $lignePave <= $ligneJusque; $lignePave++)
				{
					for($colonnePave = $colonne; $colonnePave <= $colonneJusque; $colonnePave++)
					{
						//Si la case est vide
						if($sudoku[$lignePave][$colonnePave]->chiffre == -1)
						{			
							
							//On ajoute les candidats possibles dans un tableau
								if($sudoku[$lignePave][$colonnePave]->chiffresPossibles[$chiffre] == 1)
								{
									$nbChiffresPossibles[$chiffre]++;
									$ligneCandidatUnique[$chiffre] = $lignePave;
									$colonneCandidatUnique[$chiffre] = $colonnePave;
								}
						}
					}
				}
			
				//On a terminé de parcourir le pavé, on analyse le tableau pour savoir s'il y a des candidats uniques
				if($nbChiffresPossibles[$chiffre] == 1)
				{	
					//Le pavé contient une case qui est la seule à pouvoir contenir le chiffre : elle le contient donc.
					$ligneContient = $ligneCandidatUnique[$chiffre];
					$colonneContient = $colonneCandidatUnique[$chiffre];
					$sudoku[$ligneContient][$colonneContient]->chiffre = $chiffre;
					$sudoku[$ligneContient][$colonneContient]->chiffresPossibles = RemplirTableau(-1);
					EliminerChiffre($chiffre, $ligneContient, $colonneContient);
					$casesRemplies++;
					if($expliquer == 1) echo 'Chiffre <strong>'.$chiffre.'</strong> dans la case placé <strong>L'.$ligneContient.' C'.$colonneContient.'</strong>, seul '.$chiffre.' dans le pavé<br />';
				}
			}
		}                                                                   
	}
	return $casesRemplies;
}

//Jumeaux ou triplés (ligne)
function JumeauxLigne($sudoku, $ligne, $colonneDepart, $chiffre, $expliquer)
{
	$casesEliminees = 0;
	
	//Recherche de jumeaux sur la même ligne
	$chiffreApparait = 0;
	for($colonne = $colonneDepart; $colonne <= $colonneDepart+2; $colonne++)
	{
			//Si la case a pour chiffre possible Chiffre, le compteur est incrémenté
			if($sudoku[$ligne][$colonne]->chiffresPossibles[$chiffre] == 1)
			{
				$chiffreApparait++;
			}
	}
		
	$utile = 0;
	//On regarde si le chiffre apparait plus d'une fois dans la ligne
	if($chiffreApparait > 1)
	{
		//Si c'est le cas, on vérifie si les autres cases du pavé contiennent aussi ce chiffre
		$ligneDe = Pave($ligne);
		$ligneA = $ligneDe + 2;
			
		$colonneDe = Pave($colonneDepart);
		$colonneA = $colonneDe + 2;
		
		$chiffreContenuPave = 0;
		
		for($lignePave = $ligneDe; $lignePave <= $ligneA; $lignePave++)
		{
			for($colonnePave = $colonneDe; $colonnePave <= $colonneA; $colonnePave++)
			{
				if($sudoku[$lignePave][$colonnePave]->chiffresPossibles[$chiffre] == 1)
					$chiffreContenuPave++;
			}
		}
				
		//Si on a trouvé le même nombre qu'avant, ça veut dire que les seuls dans ce pavé sont ceux trouvés à la première étape
		if($chiffreContenuPave == $chiffreApparait)
		{								
			//On peut donc éliminer le chiffre de la liste des chiffres possibles des cases sur la même ligne
			for($colonneEliminer = 1; $colonneEliminer <= 9; $colonneEliminer++)
			{
				if($sudoku[$ligne][$colonneEliminer]->chiffresPossibles[$chiffre] == 1 && ($colonneEliminer < $colonneDe || $colonneEliminer > $colonneA))
				{
					$sudoku[$ligne][$colonneEliminer]->chiffresPossibles[$chiffre] = -1;
					$casesEliminees++;
					$utile = 1;
				}
			}
		}
	}
	
	//Si la technique n'a pas permis d'éliminer de nouveaux candidats, il n'y a rien à afficher
	if($utile == 1)
	{
		if($expliquer == 1) echo '<p>Les candidats <strong>'.$chiffre.'</strong>, alignés à la ligne <strong>'.$ligne.'</strong> 
		(de la case <strong>L'.$ligne.' C'.$colonneDe.'</strong> à la case <strong>L'.$ligne.' C'.$colonneA.'</strong>) permettent d\'éliminer ce candidat des autres cases de la ligne</p>';
	}
	
	return $casesEliminees;
}

//Jumeaux ou triplés (colonne)
function JumeauxColonne($sudoku, $ligneDepart, $colonne, $chiffre, $expliquer)
{
	$casesEliminees = 0;
	
	//Recherche de jumeaux sur la même colonne
	$chiffreApparait = 0;
	
	for($ligne = $ligneDepart; $ligne <= $ligneDepart+2; $ligne++)
	{
			//Si la case a pour chiffre possible Chiffre, le compteur est incrémenté
			if($sudoku[$ligne][$colonne]->chiffresPossibles[$chiffre] == 1)
			{
				$chiffreApparait++;
			}
	}
	
	$utile = 0;
	//On regarde si le chiffre apparait plus d'une fois dans la ligne
	if($chiffreApparait > 1)
	{
		//Si c'est le cas, on vérifie si les autres cases du pavé contiennent aussi ce chiffre
		$ligneDe = Pave($ligneDepart);
		$ligneA = $ligneDe + 2;
			
		$colonneDe = Pave($colonne);
		$colonneA = $colonneDe + 2;
		
		$chiffreContenuPave = 0;
		
		for($lignePave = $ligneDe; $lignePave <= $ligneA; $lignePave++)
		{
			for($colonnePave = $colonneDe; $colonnePave <= $colonneA; $colonnePave++)
			{
				if($sudoku[$lignePave][$colonnePave]->chiffresPossibles[$chiffre] == 1)
					$chiffreContenuPave++;
			}
		}
				
		//Si on a trouvé le même nombre qu'avant, ça veut dire que les seuls dans ce pavé sont ceux trouvés à la première étape
		if($chiffreContenuPave == $chiffreApparait)
		{								
			//On peut donc éliminer le chiffre de la liste des chiffres possibles des cases sur la même colonne
			for($ligneEliminer = 1; $ligneEliminer <= 9; $ligneEliminer++)
			{
				if($sudoku[$ligneEliminer][$colonne]->chiffresPossibles[$chiffre] == 1 && ($ligneEliminer < $ligneDe || $ligneEliminer > $ligneA))
				{
					$sudoku[$ligneEliminer][$colonne]->chiffresPossibles[$chiffre] = -1;
					$casesEliminees++;
					$utile = 1;
				}
			}
		}
	}
			
	if($utile == 1)
	{
		if($expliquer == 1) echo '<p>Les candidats <strong>'.$chiffre.'</strong>, alignés à la colonne <strong>'.$colonne.'</strong> 
		(de la case <strong>L'.$ligneDe.' C'.$colonne.'</strong> à la case <strong>L'.$ligneA.' C'.$colonne.'</strong>) permettent d\'éliminer ce candidat des autres cases de la colonne</p>';
	}
	
	return $casesEliminees;
}

//Interaction entre régions (Ligne)
function InteractionEntreRegionsLigne($sudoku, $expliquer)
{
	$casesEliminees = 0;
	
	//Pour chaque chiffre de 1 à 9
	for($chiffre = 1; $chiffre <= 9; $chiffre++)
	{	
		//On parcours le sudoku ligne par ligne
		for($ligne = 1; $ligne <= 9; $ligne++)
		{
			$presentDansLigne[1] = 0;
			$presentDansLigne[2] = 0;
			$presentDansLigne[3] = 0;
			$region = 1;
			$regionEliminer = 0;
			
			for($colonne = 1; $colonne <= 9; $colonne++)
			{	
				//Si le chiffre fait partie des chiffres possible de la case, il est présent dans la ligne. On retient sa région
				if($sudoku[$ligne][$colonne]->chiffresPossibles[$chiffre] == 1)
				{
					$presentDansLigne[$region]++;
				}
				
				//On passe à la région suivante après avoir atteint la fin de l'une d'entre elles
				if($colonne == 3 || $colonne == 6 || $colonne == 9)
				{
					$region++;
				}
			}
			
			//Si le chiffre n'est présent que dans une des régions, et pas dans les deux autres
			if($presentDansLigne[1] == 0 && $presentDansLigne[2] == 0 && $presentDansLigne[3] != 0)
			{
				$regionEliminer = 3;
				$colonneDe = 7;
			}
			elseif($presentDansLigne[1] == 0 && $presentDansLigne[2] != 0 && $presentDansLigne[3] == 0)
			{
				$regionEliminer = 2;
				$colonneDe = 4;
			}
			elseif($presentDansLigne[1] != 0 && $presentDansLigne[2] == 0 && $presentDansLigne[3] == 0)
			{
				$regionEliminer = 1;
				$colonneDe = 1;
			}
			
			if($regionEliminer != 0)
			{
				//On peut éliminer de la liste des candidats possible ce chiffre dans les autres cases de la région n'appartenant pas à la ligne
				$ligneDe = Pave($ligne);
				$ligneA = $ligneDe+2;
				
				$colonneA = $colonneDe+2;
				
				$casesElimineesActuel = 0;
				
				for($ligneEliminer = $ligneDe; $ligneEliminer <= $ligneA; $ligneEliminer++)
				{
					if($ligneEliminer != $ligne)
					{
						for($colonneEliminer = $colonneDe; $colonneEliminer <= $colonneA; $colonneEliminer++)
						{
							if($sudoku[$ligneEliminer][$colonneEliminer]->chiffresPossibles[$chiffre] == 1)
							{
								$sudoku[$ligneEliminer][$colonneEliminer]->chiffresPossibles[$chiffre] = 0;
								$casesEliminees++;
								$casesElimineesActuel++;
								if($expliquer == 1) echo '<p>Élimination du candidat <strong>'.$chiffre.'</strong> à la case L'.$ligneEliminer.' C'.$colonneEliminer.'</p>';
							}
						}
					}
				}
				
				if($casesElimineesActuel > 0 && $expliquer == 1)
				{
					echo '<p>Le candidat <strong>'.$chiffre.'</strong> n\'étant pas présent à la ligne <strong>'.$ligne.'</strong> dans les deux autres régions.</p>';
				}
			}
		}
	}
	
	return $casesEliminees;
}

//Interaction entre régions (Colonne)
function InteractionEntreRegionsColonne($sudoku, $expliquer)
{
	$casesEliminees = 0;
	
	//Pour chaque chiffre de 1 à 9
	for($chiffre = 1; $chiffre <= 9; $chiffre++)
	{	
		//On parcours le sudoku colonne par colonne
		for($colonne = 1; $colonne <= 9; $colonne++)
		{
			$presentDansColonne[1] = 0;
			$presentDansColonne[2] = 0;
			$presentDansColonne[3] = 0;
			$region = 1;
			$regionEliminer = 0;
			
			for($ligne = 1; $ligne <= 9; $ligne++)
			{	
				//Si le chiffre fait partie des chiffres possible de la case, il est présent dans la colonne. On retient sa région.
				if($sudoku[$ligne][$colonne]->chiffresPossibles[$chiffre] == 1)
				{
					$presentDansColonne[$region]++;
				}
				
				//On passe à la région suivante après avoir atteint la fin de l'une d'entre elle.
				if($ligne == 3 || $ligne == 6 || $ligne == 9)
				{
					$region++;
				}
			}
			
			//Si le chiffre n'est présent que dans une des régions, et pas dans les deux autres
			if($presentDansColonne[1] == 0 && $presentDansColonne[2] == 0 && $presentDansColonne[3] != 0)
			{
				$regionEliminer = 3;
				$ligneDe = 7;
			}
			elseif($presentDansColonne[1] == 0 && $presentDansColonne[2] != 0 && $presentDansColonne[3] == 0)
			{
				$regionEliminer = 2;
				$ligneDe = 4;
			}
			elseif($presentDansColonne[1] != 0 && $presentDansColonne[2] == 0 && $presentDansColonne[3] == 0)
			{
				$regionEliminer = 1;
				$ligneDe = 1;
			}
			
			if($regionEliminer != 0)
			{
				//On peut éliminer de la liste des candidats possible ce chiffre dans les autres cases de la région n'appartenant pas à la colonne
				$colonneDe = Pave($colonne);
				$colonneA = $colonneDe+2;
				
				$ligneA = $ligneDe+2;
				
				$casesElimineesActuel = 0;
				
				for($colonneEliminer = $colonneDe; $colonneEliminer <= $colonneA; $colonneEliminer++)
				{
					if($colonneEliminer != $colonne)
					{
						for($ligneEliminer = $ligneDe; $ligneEliminer <= $ligneA; $ligneEliminer++)
						{
							if($sudoku[$ligneEliminer][$colonneEliminer]->chiffresPossibles[$chiffre] == 1)
							{
								$sudoku[$ligneEliminer][$colonneEliminer]->chiffresPossibles[$chiffre] = 0;
								$casesEliminees++;
								$casesElimineesActuel++;
								if($expliquer == 1) echo '<p>Élimination du candidat <strong>'.$chiffre.'</strong> à la case L'.$ligneEliminer.' C'.$colonneEliminer.'</p>';
							}
						}
					}
				}
				
				if($casesElimineesActuel > 0 && $expliquer == 1)
				{
					echo '<p>Le candidat <strong>'.$chiffre.'</strong> n\'étant pas présent à la colonne <strong>'.$colonne.'</strong> dans les deux autres régions.</p>';
				}
				
			}
		}
	}
	
	return $casesEliminees;
}

?>
