<?php
//echo $_POST['expliquer'];
//DÉFINITION DE LA CLASSE CASES
//Contient l'abscisse, l'ordonnée, le chiffre contenu (s'il est donné), la liste des chiffres possibles (sinon) de chaque case, et si le chiffre a été entré par l'utilisateur ou trouvé par le programme
class Cases
{
	public $x;
	public $y;
	public $chiffresPossibles;
	public $chiffre;
	public $original;
	
	//Appelé lors de la création d'une instance, permet d'initaliser la case avec les bonnes valeurs
	function __construct($x, $y, $chiffre)
	{
		$this->x = $x;
		$this->y = $y;
		//Si l'utilisateur a donné le chiffre de la case
		if($chiffre)
		{
			$this->chiffre = $chiffre;
			$this->original = 1;
			for($i = 1; $i < 10; $i++)
				$this->chiffresPossibles = RemplirTableau(-1);
		}
		else
		{
			$this->chiffre = -1;
			$this->original = 0;
			$this->chiffresPossibles = RemplirTableau(1);
		}
	}
	
	//Remplissage de la case si elle n'a plus qu'une seule valeur possible
	public function Remplir($expliquer)
	{
		$nombreChiffresPossibles = 0;
		for($i = 1; $i < 10; $i++)
		{
			if($this->chiffresPossibles[$i] == 1)	$nombreChiffresPossibles++;
		}
		
		if($nombreChiffresPossibles == 1)
		{
			$this->chiffre = array_search(1, $this->chiffresPossibles);
			$this->chiffresPossibles = RemplirTableau(-1);
			EliminerChiffre($this->chiffre, $this->x, $this->y);
			if($expliquer == 1)	echo 'Chiffre <strong> '.$this->chiffre.'</strong> placé dans la case <strong>L'.$this->x.' C'.$this->y.'</strong><br />'; 
			return 1;
		}
	}
	
	//Affichage des données de la case
	public function Afficher()
	{
		echo "Case [$this->x, $this->y] : ";
		if($this->chiffre != -1)
			echo "Contient le chiffre $this->chiffre";
		else
		{
			echo "Liste des chiffres possibles : ";
			for($i = 1; $i < 10; $i++)
			{
				if($this->chiffresPossibles[$i] == 1)
					echo $i.' ';
			}
		}
		echo "<br />";
	}
}

//DÉFINITIONS DES FONCTIONS

//Fonction vérifiant si le sudoku a été résolu, en regardant si toutes les cases ont été remplies
function VerifierResolu($sudoku)
{
	$nonResolu = 0;
	for($i = 1; $i <= 9 && $nonResolu != 2; $i++)
	{
		for($j = 1; $j <= 9 && $nonResolu != 2; $j++)
		{
			//Si la case n'est pas encore rempli, le sudoku n'a pas été résolu
			if($sudoku[$i][$j]->chiffre == -1)
			{
				$nonResolu = 1;
				
				//On en profite pour vérifier si la case a encore des candidats possibles
				$chiffre = 1;
				
				//On s'arrête dès qu'on a trouvé un chiffre possible, ou que $chiffre vaut 10
				while($chiffre <= 9 && $sudoku[$i][$j]->chiffresPossibles[$chiffre] == -1)
				{
					$chiffre++;
				}
				
				//Si $chiffre vaut 10, on n'a trouvé aucun candidat possible. Le Sudoku ne peut pas être résolu
				if($chiffre == 10)
				{
					$nonResolu = 2;
				}
			}
		}
	}
	return $nonResolu;
}

//Fonction de vérification des chiffres à éliminer dans une case, en analysant son voisinage
function VerifierElimination($sudoku, $i, $j)
{
	for($colonne = 1; $colonne <= 9; $colonne++)
	{
		//Si la case n'est pas vide, on peut retirer le chiffre qu'elle contient de la liste des valeurs possibles
		if($sudoku[$i][$colonne]->chiffre != -1)
		{
			$chiffreAbsent = $sudoku[$i][$colonne]->chiffre;
			$sudoku[$i][$j]->chiffresPossibles[$chiffreAbsent] = -1;
		}
	}
			
	//Puis la colonne
	for($ligne = 1; $ligne <= 9; $ligne++)
	{
		//Si la case n'est pas vide, on peut retirer le chiffre qu'elle contient de la liste des valeurs possibles
		if($sudoku[$ligne][$j]->chiffre != -1)
		{
			$chiffreAbsent = $sudoku[$ligne][$j]->chiffre;
			$sudoku[$i][$j]->chiffresPossibles[$chiffreAbsent] = -1;
		}
	}
			
	//Puis le pavé
	//Determination du pavé à parcouvrir :
	$ligneDe = Pave($i);
	$colonneDe = Pave($j);
	
	$ligneA = $ligneDe + 2;
	$colonneA = $colonneDe + 2;
	
	//Parcours du pavé déterminé
	for($ligne = $ligneDe; $ligne <= $ligneA; $ligne++)
	{
		for($colonne = $colonneDe; $colonne <= $colonneA; $colonne++)
		{
			//Si la case n'est pas vide, on peut retirer le chiffre qu'elle contient de la liste des valeurs possibles
			if($sudoku[$ligne][$colonne]-> chiffre != -1)
			{
				$chiffreAbsent = $sudoku[$ligne][$colonne]->chiffre;
				$sudoku[$i][$j]->chiffresPossibles[$chiffreAbsent] = -1;
			}
		}
	}
}

//Fonction cherchant les cases vides qui reconstruit ensuite leur tableau de chiffres possibles
function VerifierEliminationCasesVides($sudoku)
{
	for($ligne = 1; $ligne <= 9; $ligne++)
	{
		for($colonne = 1; $colonne <= 9; $colonne++)
		{
			if($sudoku[$ligne][$colonne]->chiffre == -1)
			{
				VerifierElimination($sudoku, $ligne, $colonne);
			}
		}
	}
}

//Fonction permettant d'éliminer de la liste des chiffres possibles des autres cases le chiffre qui vient d'être trouvé dans une case
function EliminerChiffre($chiffre, $ligne, $colonne)
{
	global $sudoku;
	
	//Ligne
	for($j = 1; $j <= 9; $j++)
	{
		$sudoku[$ligne][$j]->chiffresPossibles[$chiffre] = -1;
	}
	
	//Colonne
	for($i = 1; $i <= 9; $i++)
	{
		$sudoku[$i][$colonne]->chiffresPossibles[$chiffre] = -1;
	}
	
	//Pavé
	$ligneDe = Pave($ligne);
	$colonneDe = Pave($colonne);
	
	$ligneA = $ligneDe + 2;
	$colonneA = $colonneDe + 2;
	
	for($lignePave = $ligneDe; $lignePave <= $ligneA; $lignePave++)
	{
		for($colonnePave = $colonneDe; $colonnePave <= $colonneA; $colonnePave++)
		{
			$sudoku[$lignePave][$colonnePave]->chiffresPossibles[$chiffre] = -1;
		}
	}
	
	
}

//Fonction de remplissage d'un tableau des indices 1 à 9 avec la valeur passée en paramètre
function RemplirTableau($valeur)
{
	for($i = 1; $i < 10; $i++)
	{
		$tableau[$i] = $valeur;
	}
	
	return $tableau;
}


//Fonction d'affichage du Sudoku en HTML
function AfficherSudoku($sudoku)
{
	echo '<table class="tableauresolu">';
	for($i = 1; $i < 10; $i++)
	{
		echo '<tr>';
		for($j = 1; $j < 10; $j++)
		{
			$style = "defaut";
			
			//Bordure plus épaisse pour les "bords" des pavés
			if($i == 3 || $i == 6)	$style .= ' bas ';
			if($j == 3 || $j == 6)	$style .= ' droite';
			
			//Case remplie par le programme
			if($sudoku[$i][$j]->chiffre != -1 && $sudoku[$i][$j]->original == 0)	
			{
				$style .= " resolu";
				echo '<td class="'.$style.'">'.$sudoku[$i][$j]->chiffre.'</td>';
			}
			//Case remplie par l'utilisateur
			elseif($sudoku[$i][$j]->chiffre != -1)	
			{
				$style .= " connu";
				echo '<td class="'.$style.'">'.$sudoku[$i][$j]->chiffre.'</td>';
			}
			//Case pas remplie
			else
			{
				$style .= " inconnu";
				echo '<td class="'.$style.'">';
				//Affichage des candidats possibles
				for($chiffre = 1; $chiffre <= 9; $chiffre++)
				{
					if($sudoku[$i][$j]->chiffresPossibles[$chiffre] == 1)
					{
						echo $chiffre.' ';
					}
				}
				echo '</td>';
			}
		}
		echo "</tr>\n";
	}
	echo '</table>';   
}

//Fonction retournant la première/ligne colonne du pavé où se trouve la ligne/colonne donnés en paramètre
function Pave($i)
{
	if($i >= 1 && $i <= 3)
		return 1;
	if($i >= 4 && $i <= 6)
		return 4;
	if($i >= 7 && $i <= 9)
		return 7;
}

//Fonction d'application de la méthode du seul candidat (moins de répétitions dans le code)
function AppliquerSeulCandidat($sudoku, $expliquer)
{
	$casesRempliesTotal = 0;
	
	if($expliquer == 1)
	{
		$technique = 'seulcandidat';
		?>
		<p><strong><span onmouseover="afficherExplications('<?php echo $technique ?>');" onmouseout="cacherExplications();" class="technique">Technique du seul candidat</span></strong></p>
		<?php
	}
	
	do
	{
		echo '<p>';
		$casesRemplies = SeulCandidat($sudoku, $expliquer);
		echo '</p>';
		
		$casesRempliesTotal += $casesRemplies;
	}while($casesRemplies > 0);
	
	if($expliquer == 1 && $casesRempliesTotal > 0)
	{
		AfficherSudoku($sudoku);
	}
	elseif($expliquer == 1)
	{
		echo '<p>Aucune case n\'a pu être remplie.</p>';
	}
}

//Fonction d'application de la méthode du candidat unique (moins de répétition dans le code)
function AppliquerCandidatUnique($sudoku, $expliquer)
{
		if($expliquer == 1)
		{
			$technique = 'candidatunique';
			?>
			<p><strong><span onmouseover="afficherExplications('<?php echo $technique ?>');" onmouseout="cacherExplications();" class="technique">Technique du candidat unique</span></strong></p>
			<?php
		}
		
		echo '<p>';
		$casesRemplies = CandidatUnique($sudoku, $expliquer);
		echo '</p>';
		
		if($expliquer == 1 && $casesRemplies > 0)
		{
			AfficherSudoku($sudoku);
		}
		elseif($expliquer == 1)
		{
			echo '<p>Aucune case n\'a pu être remplie</p>';
		}
		
		return $casesRemplies;
}

//DEBUT DU PROGRAMME

//Lecture de chaque case pour la remplir
$case = 1;
for($i = 1; $i < 10; $i++)
{
	for($j = 1; $j < 10; $j++)
	{
		$sudoku[$i][$j] = new Cases($i, $j, $_POST["case$case"]);
		$case++;
	}
}

//Mise en place de l'explication si l'utilisateur l'a demandée
if($_POST['expliquer'] == 'true')
{
	$expliquer = 1;
	echo '<p>Résolution étape par étape du Sudoku<br />Passez le curseur sur le nom d\'une technique pour en avoir une explication.</p>';
	echo '<div id="explicationTechnique" onmouseover="afficherExplications(\'actuel\');" onmouseout="cacherExplications();"></div>';
}
else
{
	$expliquer = 0;
}

//Div contenant les coordonnées de la case survolée
echo '<div id="coordonnees">L1C1</div>';

//PHASE DE RÉSOLUTION DU SUDOKU

//Chaque technique est appliquée jusqu'à ne plus donner aucun résultat. 
//Le sudoku est ensuite vérifié : s'il n'est pas résolu, on passe à la technique suivante

$casesRemplies = 0;
$nonResolu = 0;

//Inclusion des fichiers contenant les techniques de résolution
include('techniques.php');

//Il est peu probable que l'utilisateur se soit amusé à rentrer un Sudoku déjà résolu, mais vérifions quand même
$nonResolu = VerifierResolu($sudoku);

if($nonResolu == 1)
{	
	//Technique du seul candidat
	AppliquerSeulCandidat($sudoku, $expliquer);
}

$nonResolu = VerifierResolu($sudoku);

if($nonResolu == 1)
{	
	do
	{
		//Candidat unique
		$casesRemplies = AppliquerCandidatUnique($sudoku, $expliquer);
		
		//Recherche des conséquences de l'application de la technique du candidat unique (si elle a donné des résultats)
		if($casesRemplies > 0)	AppliquerSeulCandidat($sudoku, $expliquer);
		
	}while($casesRemplies > 0);
}

$nonResolu = VerifierResolu($sudoku);

if($nonResolu == 1)
{
	$jumeaux = 0;
	
	do
	{
		if($expliquer == 1)	
		{
			$technique = 'jumeaux';
			?>
			<p><strong><span onmouseover="afficherExplications('<?php echo $technique ?>');" onmouseout="cacherExplications();" class="technique">Technique des jumeaux en ligne</span></strong></p>
			<?php
		}
		do
		{
			//Jumeaux en ligne
			$casesElimineesTotal = 0;
			$casesRemplies = 0;
			
			for($ligne = 1; $ligne <= 9; $ligne++)
			{
				for($colonne = 1; $colonne <= 7; $colonne+=3)
				{
					for($chiffre = 1; $chiffre <= 9; $chiffre++)
					{	
						$casesEliminees = JumeauxLigne($sudoku, $ligne, $colonne, $chiffre, $expliquer);
						$casesElimineesTotal += $casesEliminees;
										
						if($casesEliminees > 0)
						{
							if($expliquer == 1)	AfficherSudoku($sudoku);
							
							//Recherche des conséquences de l'application de la technique des jumeaux (si elle a donné des résultats)
							VerifierEliminationCasesVides($sudoku);
							$casesRemplies = AppliquerCandidatUnique($sudoku, $expliquer);
							if($casesRemplies > 0)	AppliquerSeulCandidat($sudoku, $expliquer);
							if($expliquer == 1)	
							{
								$technique = 'jumeaux';
								?>
								<p><strong><span onmouseover="afficherExplications('<?php echo $technique ?>');" onmouseout="cacherExplications();" class="technique">Technique des jumeaux en ligne</span></strong></p>
								<?php
							}
						}
					}
				}
			}
		}while($casesRemplies > 0 || $casesEliminees > 0);
		
		if($expliquer == 1)	
		{
			echo '<p>Aucun candidat n\'a pu être éliminé</p>';
			$technique = 'jumeaux';
			?>
			<p><strong><span onmouseover="afficherExplications('<?php echo $technique ?>');" onmouseout="cacherExplications();" class="technique">Technique des jumeaux en colonne</span></strong></p>
			<?php
		}
		
		do{
			//Jumeaux en colonne
			$casesElimineesTotal = 0;
			$casesRemplies = 0;
			
			for($colonne = 1; $colonne <= 9; $colonne++)
			{
				for($ligne = 1; $ligne <= 7; $ligne+=3)
				{
					for($chiffre = 1; $chiffre <= 9; $chiffre++)
					{
						$casesEliminees = JumeauxColonne($sudoku, $ligne, $colonne, $chiffre, $expliquer);
						$casesElimineesTotal += $casesEliminees;
						
						if($casesEliminees > 0)
						{
							if($expliquer == 1)	AfficherSudoku($sudoku);
							
							//Recherche des conséquences de l'application de la technique des jumeaux (si elle a donné des résultats)
							VerifierEliminationCasesVides($sudoku);
							$casesRemplies = AppliquerCandidatUnique($sudoku, $expliquer);
							if($casesRemplies > 0)	AppliquerSeulCandidat($sudoku, $expliquer);
							if($expliquer == 1)
							{
								$technique = 'jumeaux';
								?>
								<p><strong><span onmouseover="afficherExplications('<?php echo $technique ?>');" onmouseout="cacherExplications();" class="technique">Technique des jumeaux en colonne</span></strong></p>
								<?php
							}
						}
						
					}
				}
			}
			
			
		}while($casesRemplies > 0 || $casesEliminees > 0);
		
		if($expliquer == 1)
		{
			echo '<p>Aucun candidat n\'a pu être éliminé</p>';
		}
			
		$jumeaux++;
		$nonResolu = VerifierResolu($sudoku);
		
	}while($jumeaux < 2 && $nonResolu == 1);
}

$nonResolu = VerifierResolu($sudoku);

if($nonResolu == 1)
{
	do{
		if($expliquer == 1)	
		{
			$technique = 'interactions';
			?>
			<p><strong><span onmouseover="afficherExplications('<?php echo $technique ?>');" onmouseout="cacherExplications();" class="technique">Technique des interactions entre régions par ligne</span></strong></p>
			<?php
		}
		$casesRemplies = 0;
		
		$casesEliminees = InteractionEntreRegionsLigne($sudoku, $expliquer);
		
		if($casesEliminees > 0)
		{
			if($expliquer == 1)	AfficherSudoku($sudoku);
			//Recherche des conséquences de l'application de la technique des interactions (si elle a donné des résultats)
			VerifierEliminationCasesVides($sudoku);
			$casesRemplies = AppliquerCandidatUnique($sudoku, $expliquer);
			if($casesRemplies > 0)	AppliquerSeulCandidat($sudoku, $expliquer);
		}
		elseif($expliquer == 1)
		{
			echo '<p>Aucun candidat n\'a pu être éliminé</p>';
		}
		
		$nonResolu = VerifierResolu($sudoku);
		
	}while(($casesRemplies > 0 || $casesEliminees > 0) && $nonResolu == 1);
	
	if($nonResolu == 1)
	{
		do{
			if($expliquer == 1)	
			{
				$technique = 'interactions';
				?>
				<p><strong><span onmouseover="afficherExplications('<?php echo $technique ?>');" onmouseout="cacherExplications();" class="technique">Technique des interactions entre régions par colonne</span></strong></p>
				<?php
			}
			$casesRemplies = 0;
			$casesEliminees = InteractionEntreRegionsColonne($sudoku, $expliquer);
			if($casesEliminees > 0)
			{
				if($expliquer == 1)	AfficherSudoku($sudoku);
				//Recherche des conséquences de l'application de la technique des interactions (si elle a donné des résultats)
				VerifierEliminationCasesVides($sudoku);
				$casesRemplies = AppliquerCandidatUnique($sudoku, $expliquer);
				if($casesRemplies > 0)	AppliquerSeulCandidat($sudoku, $expliquer);
			}
			
			elseif($expliquer == 1)
			{
				echo '<p>Aucun candidat n\'a pu être éliminé</p>';
			}
			
			$nonResolu = VerifierResolu($sudoku);
			
		}while(($casesRemplies > 0 || $casesEliminees > 0) && $nonResolu == 1);
	}
}

//Si les techniques normales n'ont rien données, une seule solution : la force brute
if($nonResolu == 1)
{
	if($expliquer == 1)
	{
			$technique = 'forcebrute';
			?>
			<p><strong><span onmouseover="afficherExplications('<?php echo $technique ?>');" onmouseout="cacherExplications();" class="technique">Applications de la force brute</span></strong></p>
			<?php
	}
	include("forcebrute.php");
	ForceBrute($sudoku);
}

$nonResolu = VerifierResolu($sudoku);

//Affichage du Sudoku après résolution
//S'il n'a pu être résolu, il est incorrect, on affiche un message d'erreur
if($nonResolu == 1)
	echo '<p><strong>Impossible de trouver une solution pour ce Sudoku. Or, un Sudoku sans solution est un Sudoku invalide. Veuillez vérifier votre saisie.</strong></p>';
elseif($nonResolu == 0)
	echo '<p><strong>Sudoku résolu avec succès.</strong></p>';
else
	echo '<p><strong>Une des cases de ce Sudoku n\'a plus aucun candidat possible. Il ne peut donc pas être résolu. Veuillez vérifier votre saisie.</strong></p>';
	
AfficherSudoku($sudoku);

//Formulaire de sauvegarde du Sudoku
?>
<form action="sauvegarder.php" method="post" name="sauvegarder">
<p>
<input type="submit" value="Sauvegarder le Sudoku" onclick="return DemanderNom()" />
<input type="hidden" name="nom" />
<input type="hidden" name="sudoku" value="<?php
$case = 1;
	while($case <= 81)
	{
			if($_POST["case$case"] == '')
				echo ';';
			else
				echo $_POST["case$case"].';';
		$case++;
	}
?>
" />
</p>
</form>
<p><a href="sudoku.php">Entrer un nouveau Sudoku</a></p>
