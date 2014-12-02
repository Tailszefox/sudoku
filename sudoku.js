//Chargement d'une grille sauvegardée
function charger(sauvegarde)
{
	//Fond orange pour toutes les cases
	for(i = 0; i < 81; i++)
	{
		if(document.tableauSudoku.elements[i].value != '')
		{
			document.tableauSudoku.elements[i].style.background = "#FFCF00";
		}
	}
	
	//Les différents chiffres sont séparés par des ;
	var sudoku=sauvegarde.split(";");
	for (var i=0; i<sudoku.length-1; i++)
	{
		document.tableauSudoku.elements[i].value = sudoku[i];
	}
	
	for(i = 0; i < 81; i++)
	{
		//Fond blanc pour les cases remplies
		if(document.tableauSudoku.elements[i].value != '')
		{
			document.tableauSudoku.elements[i].style.background = "#FFFFFF";
		}
	}
}

//Vérifie qu'un Sudoku a bien été sélectionné avant de l'effacer, et demande confirmation
function verifSelectionne()
{
	var selectionne = document.sudokuSauvegardes.sauvegardes.selectedIndex;
	if(selectionne == -1)
	{
		alert("Sélectionnez le sudoku à supprimer dans la liste.");
		return false;
	}
	else
	{
		var reponse = confirm("Voulez-vous vraiment effacer ce Sudoku ?");
		return reponse;
	}
}

//Confirmation de remise à zéro du formulaire
function confirmation()
{
	var reponse = confirm("Voulez-vous vraiment effacer le Sudoku affiché ?");
	if(reponse == true)
	{
		var i;
		//Tout le monde en orange
		for(i = 0; i < 81; i++)
		{
			document.tableauSudoku.elements[i].style.background = "#FFCF00";
		}
	}
	return reponse;
}

//Vérifie qu'au moins 17 cases ont été remplies (minimum possible pour ne pas tomber sur un Sudoku à plusieurs solutions)
//Vérifie également que le Sudoku est valide
//Permet de ne pas essayer de résoudre un Sudoku où deux chiffres sont dans le même voisinage, qui ne comporte par définition aucune solution
function verifierSudoku()
{
	var i;
	var casesRemplies = 0;
	
	for(i = 0; i < 81; i++)
	{
		if(document.tableauSudoku.elements[i].value != '')
		{
			casesRemplies++;
		}
	}
	
	if(casesRemplies < 17)
	{	
		var reponse = confirm("Votre Sudoku comporte moins de 17 cases remplies. Un tel Sudoku aura très probablement plusieurs solutions, ce qui le rend invalide. Voulez-vous quand même essayer de le résoudre ?");
		if(reponse == false)
		{
			return false;
		}
	}
	
	var valide = 1;
	
	//Parcourir par ligne
	var numeroCase = 1;
	tableauChiffre = new Array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
	
	while(numeroCase <= 81 && valide == 1)
	{
		chiffre = document.tableauSudoku.elements[numeroCase-1].value;
		tableauChiffre[chiffre]++;
		
		if(numeroCase % 9 == 0)
		{
			var i = 1;
			while(i <= 9 && valide == 1)
			{
				if(tableauChiffre[i] > 1)
				{
					valide = 0;
					var ligneFautive =  numeroCase/9;
					alert("Le Sudoku entré est invalide : il comporte plusieurs "+i+" à la ligne "+ligneFautive+".");
				}
				i++;
			}
			tableauChiffre = new Array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
		}
		
		numeroCase++;	
	}
	
	//Parcourir par colonne
	var numeroCase = 1;
	tableauChiffre = new Array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
	
	while(numeroCase <= 81 && valide == 1)
	{
		chiffre = document.tableauSudoku.elements[numeroCase-1].value;
		tableauChiffre[chiffre]++;
	
		if(numeroCase >= 73 && numeroCase <= 81)
		{
			var i = 1;
			while(i <= 9 && valide == 1)
			{
			if(tableauChiffre[i] > 1)
				{
					valide = 0;
					var colonneFautive = numeroCase - 72;
					alert("Le Sudoku entré est invalide : il comporte plusieurs "+i+" dans la colonne "+colonneFautive+".");
				}
				i++;
			}
			
			if(numeroCase < 81)
			{
				numeroCase = numeroCase - 71;
			}
			else
			{
				numeroCase = 82;
			}
			tableauChiffre = new Array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
		}
		else
		{
			numeroCase = numeroCase+9;
		}
	}
		
	//Parcourir par pavé
	var numeroCase = 1;
	var pave = 1;
	tableauChiffre = new Array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
	while(numeroCase <= 81 && valide == 1)
	{
		chiffre = document.tableauSudoku.elements[numeroCase-1].value;
		tableauChiffre[chiffre]++;
		
		if(numeroCase == 21 || numeroCase == 24 || numeroCase == 27 || numeroCase == 48 || numeroCase == 51 || numeroCase == 54 || numeroCase == 75 || numeroCase == 78 || numeroCase == 81)
		{
			var i = 1;
			while(i <= 9 && valide == 1)
			{
				if(tableauChiffre[i] > 1)
				{
					valide = 0;
					alert("Le Sudoku entré est invalide : il comporte plusieurs "+i+" dans le pavé "+pave+".");
				}
				i++;
			}
			
			if(numeroCase == 27 || numeroCase == 54 || numeroCase == 81)
			{
				numeroCase++;
			}
			else
			{
				numeroCase = numeroCase - 17;
			}
			tableauChiffre = new Array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
			pave++;
		}
		else
		{
			if(numeroCase%3 == 0)
			{
				numeroCase = numeroCase + 7;
			}
			else
			{
				numeroCase++;
			}
		}
	}
		
	if(valide == 1)
	{
		if(document.tableauSudoku.activerAjax.checked)
		{

			var xhr; 
			try {  xhr = new ActiveXObject('Msxml2.XMLHTTP');   }
			catch (e) 
			{
				try {   xhr = new ActiveXObject('Microsoft.XMLHTTP');    }
				catch (e2) 
				{
					try {  xhr = new XMLHttpRequest();     }
					catch (e3) {  xhr = false;   }
				}
			}
 
			xhr.onreadystatechange  = function()
			{ 
				if(xhr.readyState  == 4)
				{
					if(xhr.status  == 200)
					{
						document.getElementById("sudokuEntier").innerHTML = xhr.responseText;
					}
					else
					{
						document.getElementById("sudokuEntier").innerHTML = "Désolé, il y a eu une erreur lors de la résolution. Essayez sans activer l'option Ajax.";
					}
				}
				else
				{
					document.getElementById("sudokuEntier").innerHTML = 'Le Sudoku est en cours de résolution, veuillez patienter.<br /><img src="chargement.gif" alt="Chargement..." />';
				}
			}; 
			
			var post = "";
			var plus = 0;
			
			for(i = 0; i < document.tableauSudoku.elements.length; i++)
			{
				if(plus == 0)
				{
					if(document.tableauSudoku.elements[i].type == 'text')
					{
						post = document.tableauSudoku.elements[i].name + "=" + document.tableauSudoku.elements[i].value;
					}
					else
					{
						alert(document.tableauSudoku.elements[i].type);
					}
					plus++;
				}
				else
				{	if(document.tableauSudoku.elements[i].type == 'text')
					{
						post = post + "&" + document.tableauSudoku.elements[i].name + "=" + document.tableauSudoku.elements[i].value;
					}
					else
					{
						if(document.tableauSudoku.elements[i].type == 'checkbox')
						{
							post = post + "&" + document.tableauSudoku.elements[i].name + "=" + document.tableauSudoku.elements[i].checked;
						}
					}
				}
			}
			
			//document.write(post);
			
			xhr.open('POST', "sudoku2ajax.php",  true); 
			 xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded"); 
			xhr.send(post); 
			
			return false;
		}
		else
		{
			return true;
		}
	}
	else
	{
		var charger=document.getElementById("chargement").style;
		charger.visibility="visible";
		return false;
	}
}

//Vérifie que le chiffre entré dans la case est bien un chiffre, et pas autre chose
function verifierChiffre(numeroElement)
{
	var valeurEntree = document.tableauSudoku.elements[numeroElement-1].value;
	var Chiffres="123456789";
	if(Chiffres.indexOf(valeurEntree) == -1)
	{
		document.tableauSudoku.elements[numeroElement-1].style.background = "#FF0000";
		document.tableauSudoku.elements[numeroElement-1].focus();
	}
	else
	{
		if(valeurEntree == '')
		{
			document.tableauSudoku.elements[numeroElement-1].style.background = "#FFCF00";
		}
		else
		{
			document.tableauSudoku.elements[numeroElement-1].style.background = "#FFFFFF";
		}
	}
}

//Fais bouger le curseur dans le Sudoku à l'aide des flèches
//Permet aussi de remplacer ou d'entrer un chiffre sans avoir besoin d'effacer le précédent
function bouger(e, numeroElement)
{
	
	var touche;
	
	if(e.keyCode)
	{
		touche = e.keyCode;
	}
	else
	{
		touche = e.charCode;
	}
	
	if(touche == 37 || touche == 38 || touche == 39 || touche == 40)
	{
		nouvelleCase = numeroElement - 1;
		if(touche == 37)
		{
			nouvelleCase -= 1;
		}
		if(touche == 38)
		{
			nouvelleCase -= 9;
		}
		if(touche == 39)
		{
			nouvelleCase += 1;
		}
		if(touche == 40)
		{
			nouvelleCase += 9;
		}
	
		if(nouvelleCase < 0 || nouvelleCase > 80)
		{
			nouvelleCase = numeroElement - 1;
		}
		
		if(document.tableauSudoku.elements[numeroElement-1].value == '?')
			document.tableauSudoku.elements[numeroElement-1].value = '';
		
		document.tableauSudoku.elements[nouvelleCase].focus();
	}
	else
	{
		chiffre = convertirChiffre(touche);
		var Chiffres="123456789";
		if(Chiffres.indexOf(chiffre) != -1)
		{
			document.tableauSudoku.elements[numeroElement-1].value = chiffre;
			document.tableauSudoku.elements[numeroElement-1].style.background = "#FFFFFF";
		}
		else
		{
			document.tableauSudoku.elements[numeroElement-1].style.background = "#FFCF00";
		}
	}
}

//Retourne le chiffre correspondant à la touche pressée
function convertirChiffre(touche)
{
  //96 à 105 : pavé numérique
	switch(touche)
	{
		case 96: 
      return "0"; 
      break;
		case 97: 
      return "1"; 
      break;
		case 98: 
      return "2"; 
      break;
		case 99: 
      return "3"; 
      break;
		case 100: 
      return "4"; 
      break;
		case 101: 
      return "5"; 
      break;
		case 102: 
      return "6"; 
      break;
		case 103: 
      return "7"; 
      break;
		case 104: 
      return "8"; 
      break;
		case 105: 
      return "9"; 
      break;
		default: 
      return String.fromCharCode(touche); 
      break;
	}
}

//Affiche "?" dans la case actuellement sélectionnée
function prendreFocus(numeroElement)
{
	
	if(document.tableauSudoku.elements[numeroElement-1].value == '')
	{
		document.tableauSudoku.elements[numeroElement-1].value = '?';
	}
}

//Vide la case si rien n 'a été entré
function perdreFocus(numeroElement)
{
	if(document.tableauSudoku.elements[numeroElement-1].value == '?' || document.tableauSudoku.elements[numeroElement-1].value == '')
	{
			document.tableauSudoku.elements[numeroElement-1].value = '';
	}
	else
	{
			document.tableauSudoku.elements[numeroElement-1].style.background = "#FFFFFF";
	}
}

//Affiche les explications sur le détail de la résolution
function afficherExplications()
{
	var explique=document.getElementById("explications").style;
	explique.visibility="visible";
}

//Cache l'explication
function cacherExplications()
{
	var explique=document.getElementById("explications").style;
	explique.visibility="hidden";
}
