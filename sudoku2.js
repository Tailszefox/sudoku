//Cache le message de chargement une fois le Sudoku résolu
function chargement()
{
	var ld=document.getElementById("chargement").style;
	ld.visibility="hidden";
}

//Demande le nom du Sudoku à sauvegarder
function DemanderNom()
{
	var nom;
	nom=prompt("Entrez le nom sous lequel sauvegarder le Sudoku :");
	if(nom == null || nom == '')
	{
		if(nom == '')
		{
			alert("Vous devez donner un nom à votre Sudoku.");
		}
		return false;
	}
	else
	{
		document.sauvegarder.nom.value = nom;
		return true;
	}
}

//Affiche une explication de la technique passée en paramètre
function afficherExplications(technique)
{
	var expliquer=document.getElementById("explicationTechnique");
	var explications;
	
	expliquer.style.visibility="visible";
	
	if(technique != 'actuel')
	{
		
		switch(technique)
		{
			case 'seulcandidat':
				explications = "<p>La technique du seul candidat est une technique de base, indispensable pour résoudre un Sudoku.<br />Elle consiste à analyser le voisinage d'une case (par voisinage, on entend la ligne, la colonne et le pavé), pour éliminer de la liste des candidats possibles tous les chiffres qui s'y trouvent déjà.	Si, dans la liste des candidats possibles de la case, il ne reste plus qu'un seul chiffre, c'est forcément ce chiffre qui est contenu dans cette case.<br /><br /><strong>Exemple</strong><br />  Pour une case donnée, on sait que celle-ci se trouve :<br />sur une ligne où se trouvent un 2, un 5 et un 7;<br />sur une colonne où se trouvent un 3, un 4 et un 6;<br />dans un pavé où se trouvent un 1 et un 8.<br />Si on élimine tous ces chiffres de la liste des candidats possibles de la case, il ne reste plus que le chiffre 9. C'est donc ce chiffre qui se trouve dans la case.";
				break;
			case 'candidatunique':
				explications = "<p>La technique du candidat unique permet de facilement trouver le chiffre contenu dans une case, même si cette case possède encore plusieurs candidats.<br />Elle consiste à comparer les candidats possibles de la case avec ceux des cases de son voisinage. Si la case est la seule à posséder un chiffre donné comme candidat, alors elle contient forcément ce chiffre.<br /><br /><strong>Exemple</strong><br />Sur une ligne donnée, il reste trois cases à remplir.<br />La première a pour candidats possibles le 1 et le 5;<br />la deuxième, le 1 et le 7;<br />la troisième, le 1 et le 5.<br />On remarque que le 7 n'apparait qu'une fois dans cette liste (dans les candidats de la seconde case). Cette seconde case contient donc forcément le chiffre 7.</p>";
				break;
			case 'jumeaux':
				explications = "<p>La technique des jumeaux ne permet pas de trouver directement le chiffre contenu dans une case, mais permet d'éliminer des candidats possibles.<br />Elle consiste à regarder la liste des candidats possibles de chaque case d'un pavé. Si un candidat n'apparait que dans des cases alignées sur la même ligne/colonne du pavé, alors il peut être éliminé de la liste des candidats possibles de toutes les autres cases de la ligne/colonne qui n'appartiennent pas au pavé.<br /><br /><strong>Exemple</strong><br />Sur un pavé, il reste trois cases à remplir.<br />La première a pour candidats possibles le 1 et le 3;<br />la seconde, le 1, le 2 et le 3;<br />la dernière, le 1, le 2 et le 3.<br />Si la seconde et la troisième case sont bien sur la même ligne/colonne, la technique peut être appliquée, le 2 étant présent uniquement dans ces deux cases. On peut alors éliminer le 2 de la liste des candidats possibles de toutes les autres cases situées sur la même ligne/colonne que ces deux cases.<br /><br />Cette technique fonctionne également avec trois cases alignées, il s'agit alors de la technique des triplés, le principe étant identique.";
				break;
			case 'interactions':
				explications = "<p>La technique des interactions entre régions est en fait l'inverse de la technique des jumeaux.<br />Elle consiste à regarder la liste des candidats possibles de deux régions alignées. Si une ligne ou une colonne ne contient pas un candidat, mais que ce candidat est contenu dans les autres cases, on peut aller le retirer de la liste des candidats des cases appartenant au troisième pavé aligné, n'appartenant pas à la ligne/colonne.<br /><br /><strong>Exemple</strong><br />Sur deux pavés alignés, on constate que la deuxième ligne de ces pavés n'a aucune case possédant le candidat 5 dans la liste de leurs candidats possibles, alors que 5 est présent comme candidat possible dans la ligne 1 et 3 de ces pavés. Alors, le candidat 5 peut être retiré de la liste des candidats possibles de toutes les cases des lignes 1 et 3 du troisième pavé.</p>";
				break;
			case 'forcebrute':
			explications = "<p>La technique de la force brute est ce qu'on pourrait appeler celle de la dernière chance. Elle n'est appliquée que si, après application de toutes les autres techniques, le Sudoku n'est toujours pas résolu.<br />Il s'agit simplement de remplir chaque case avec son premier candidat unique, en espérant que ce soit le bon. Si, par la suite, une case ne contient plus aucun candidat possible, cela signifie qu'une erreur a été faite et qu'une case ne contient pas le bon chiffre.<br />Il est donc nécessaire de revenir en arrière et essayer avec un autre candidat possible, jusqu'à ce que le Sudoku soit résolu.<br /><br /><strong>Exemple</strong><br />Il reste trois cases à résoudre dans un Sudoku, ces cases étant situées sur la même ligne.<br />La première contient les candidats 1 et 5;<br />la deuxième les candidats 3 et 9;<br />la troisième les candidats 1 et 3.<br />On remplit la première case avec le chiffre 1, et la seconde avec le chiffre 3. Cependant, la dernière case n'a plus aucun candidat possible, le 1 et le 3 étant déjà présents sur la ligne.<br />Il faut alors revenir en arrière. On essaye alors le second candidat possible pour la case précédente : on remplit la seconde case avec le chiffre 9. La troisième case ne peut alors être remplique qu'avec le chiffre 3.<br />Toutes les cases étant maintenant remplies, le Sudoku est résolu.<br /><br />La force brute nécessitant souvent d'essayer une très grande quantité de combinaisons, son application n'est pas détaillée.</p>";
				break;
		}
		
		expliquer.innerHTML = explications;
	}
}

//Cache l'explication
function cacherExplications()
{
	var expliquer=document.getElementById("explicationTechnique");
	expliquer.style.visibility="hidden";
}

//Affiche les coordonnées de la case
function afficherCoordonnees(x, y)
{
	var coordonnees = document.getElementById("coordonnees");
	coordonnees.style.visibility ="visible";
	coordonnees.innerHTML = "L"+x+"C"+y;

	var divName = 'coordonnees';
	var offX = 15;
	var offY = 15;  
}

//Cache les coordonnées
function cacherCoordonnees()
{
	var coordonnees = document.getElementById("coordonnees");
	coordonnees.style.visibility ="hidden";
}
