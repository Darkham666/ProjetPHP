<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;">
		<meta charset="utf-8">
		<title>Modifier un directeur au Tour de France</title>
	</head>
	<body>

		<?php
			include_once ('../menuBarre.php');
			include_once ('../connBDD.php');
			include_once ('../fonc_test.php');
		?>

		<h2 align="center">Modifier d'un directeur</h2>

	<!-- FORMULAIRE POUR MODIFIER UN DIRECTEUR A LA BASE -->

	<?php
			
			// Initialisation des variables
			$erreur = 0;

			if(!isset($valeurTestNom)) {
				$valeurTestNom = "";
			}

			if(!isset($valeurTestPrenom)) {
				$valeurTestPrenom = "";
			}

			if(!isset($valeurTestModif)) {
				$valeurTestModif = "";
			}

			if(!isset($valeurTestNum)) {
				$valeurTestNum = "";
			}

			if(isset($_POST['MODIFIER'])) {
				
				if($_POST['num'] != "") {
					if($_POST['nom'] != "" || $_POST['prenom'] != "" ) {
						
						$erreur = 0;
						$valeurInsertion = "";
						
						if(!testNom($_POST['nom'])) {
							$erreur = 1;
							$valeurTestNom = "Veuillez saisir un nom valide.";
						}
						else {
							$valeurNom = testNom($_POST['nom']);
							$nom = $valeurNom;
							$valeurInsertion = " nom = '".$nom."'";
						}

						if(!testPrenom($_POST['prenom'])) {
							$erreur = 1;
							$valeurTestPrenom = "Veuillez saisir un prénom valide.";
						}
						else {
							$valeurPrenom = testPrenom($_POST['prenom']);
							$prenom = $valeurPrenom;
							if($valeurInsertion == "")
								// MODIFIER SEULEMENT LE PRENOM
									$valeurInsertion = $valeurInsertion." prenom = '".$prenom."'";
							else
									$valeurInsertion = $valeurInsertion.", prenom = '".$prenom."'";
						}

						if($erreur != 1) {

							$conn = OuvrirConnexion();
							$req = "INSERT INTO TDF_DIRECTEUR(n_directeur, nom, prenom) values ($num,$nom,$prenom.)";
							$cur = preparerRequete($conn, $req);
							$tab = executerRequete($cur);
							oci_commit($conn);
							
							FermerConnexion($conn);
							
							$valeurTestModif = "Directeur modifié avec succès.";
						}
						
					}
					else{
						$valeurTestNum = "Veuillez modifier au moins un champs.";
						$valeurNum = $_POST['num'];
					}
				}else{
					$valeurTestNum = "Veuillez choisir le directeur à modifier.";
				}
			}
		?>

		<form name="formModDirecteur" action="<?php $_SERVER['PHP_SELF'] ?>" method="post" >
			<div align="center" style="margin-left:20%; margin-right:20%">
				<fieldset >
					<table border=0 cellpadding=10>
						<tr>
							<td>
								Selectionnez un directeur à modifier :
							</td>
							<td>
								<?php
									$conn = OuvrirConnexion();
									$req = 'SELECT n_directeur, nom, prenom from tdf_directeur order by nom, prenom';
									$cur = preparerRequete($conn, $req);
									$tab = executerRequete($cur);
									FermerConnexion($conn);
									$nbLignes = oci_fetch_all($cur, $tab,0,-1,OCI_FETCHSTATEMENT_BY_ROW);
									
									echo "<select name='num' size=1>";
										echo "<option value=''>Selectionnez un directeur</option>";
										for ($i=0;$i<$nbLignes;$i++) {

											if($tab[$i]["N_DIRECTEUR"] == $valeurNum) {
											echo '<option value="'.$tab[$i]["N_DIRECTEUR"].'" selected>'.$tab[$i]["NOM"].' - '.$tab[$i]["PRENOM"];
											}
											else {
											echo '<option value="'.$tab[$i]["N_DIRECTEUR"].'">'.$tab[$i]["NOM"].' '.utf8_encode($tab[$i]["PRENOM"]);
											}
										  echo '</option>';
										} 	
									echo "</select> ";
								?>
							</td>
							<td>
								<font color="red"><?php echo $valeurTestNum; ?></font>
							</td>
						</tr>
						<tr>
							<td>
								Nom :
							</td>
							<td>
								<input type="text" name="nom" size=40 placeholder="Entrez un nom" value="<?php if(isset($valeurNom)) echo $valeurNom ?>">
							</td>
							<td>
								<font color="red"><?php echo $valeurTestNom; ?></font>
							</td>
						</tr>
						<tr>
							<td>
								Prénom :
							</td>
							<td>
								<input type="text" name="prenom" size=40 placeholder="Entrez un prénom" value="<?php if(isset($valeurPrenom)) echo $valeurPrenom; ?>"> 
							</td>
							<td>
								<font color="red"><?php if(isset($valeurTestPrenom)) echo $valeurTestPrenom; ?></font>
							</td>
						</tr>
						<tr>
							<td align="center" colspan=2>
								<input type='submit' name='Modifier' value='Modifier le directeur' >
								
							</td>
							<td>
								<font color='green'><?php echo $valeurTestModif; ?></font>
							</td>
						</tr>
					</table>
					
				</fieldset>
			</div>
		</form>

	</body>
</html>