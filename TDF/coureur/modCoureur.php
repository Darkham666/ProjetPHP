<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;">
		<meta charset="utf-8">
		<title>Modifier un coureur du Tour de France</title>
	</head>
	<body>

		<?php
			include_once ("../menuBarre.php");
			include_once ("../connBDD.php");
			include_once ("../fonc_test.php");
			include_once ("../log.php");
		?>

		<h2 align="center">Modification d'un coureur</h2>

	<!-- FORMULAIRE POUR MODIFIER UN COUREUR DE LA BASE -->
		
		<?php

			// Initialisation des variables
			$erreur = 0;

			if(!isset($valeurNumCoureur))
				$valeurNumCoureur = "";

			if(!isset($valeurTestSelectionCoureur))
				$valeurTestSelectionCoureur = "";

			if(!isset($valeurTestNom))
				$valeurTestNom = "";

			if(!isset($valeurTestPrenom))
				$valeurTestPrenom = "";

			if(!isset($valeurTestPays))
				$valeurTestPays = "";

			if(!isset($valeurTestModif))
				$valeurTestModif = "";

			if(!isset($valeurAnnee))
				$valeurAnnee = "";

			if(!isset($valeurPays))
				$valeurPays = "";

			if(!isset($valeurParticipation))
				$valeurParticipation = "";

			if(!isset($valeurTestAnneeNaissance)) {
				$valeurTestAnneeNaissance = "";
				$valeurPays = "";
			}

			if(isset($_POST['Modifier'])) {

				if($_POST['numCoureur'] != 'Selectionnez un coureur') {

					if($_POST['nom'] != "" || $_POST['prenom'] != "" || $_POST['annee_naissance'] != "" || $_POST['annee_tour'] != "" || $_POST['pays'] != "") {
						
						$valeurInsertion = '';
						$valeurNumCoureur = $_POST['numCoureur'];

						// NOM
							if(!testNom($_POST['nom'])) {
								$erreur = 1;
								$valeurTestNom="Veuillez saisir un nom valide.";
							}
							else {
								$nom = testNom($_POST['nom']);
								$valeurInsertion = $valeurInsertion." nom = '".toSQL($nom)."'";
							}
						
						// PRENOM
							if(!testPrenom($_POST['prenom'])) {
								$erreur = 1;
								$valeurTestPrenom = "Veuillez saisir un prénom valide.";
							}

							$prenom = testPrenom($_POST['prenom']);						
							$valeurInsertion = $valeurInsertion.", prenom = '".toSQL($prenom)."'";
						
						// ANNEE
							if($_POST['annee_naissance'] != null) {
								$annee = $_POST['annee_naissance'];
								$type = gettype($annee);

									if (($_POST['annee_tour']-$_POST['annee_naissance'])>18) {
										$valeurAnnee = $_POST['annee_naissance'];
										$valeurInsertion = $valeurInsertion.', annee_naissance = '.$valeurAnnee;
									}

									else {
										$valeurTestAnneeNaissance = "Impossible de participer au TDF si vous n'êtes pas né...";
										$erreur = 1;
									}
							}
							else {
								$erreur = 1;
								$valeurTestAnneeNaissance = "Veuillez entrer une date valide.";
							}

						// PREMIERE PARTICIPATION						
						if($_POST['annee_tour'] != "") {
							$annee_tour = $_POST['annee_tour'];
							$valeurParticipation = $annee_tour;
							if($valeurInsertion == "")
								$valeurInsertion = $valeurInsertion.' annee_tdf = '.$annee_tour;
							else
								$valeurInsertion = $valeurInsertion.', annee_tdf = '.$annee_tour;
						}
						
						// PAYS
						if($_POST['pays'] != "") {
							$pays = $_POST['pays'];
							$valeurPays = $pays;
							if($valeurInsertion == "")
								$valeurInsertion = $valeurInsertion." code_tdf = '".$pays."'";
							else
								$valeurInsertion = $valeurInsertion.", code_tdf = '".$pays."'";
						}
						
						// Longueur
							if (strlen($prenom) > 30 || strlen($nom) > 20 ) {
								$erreur = 1;
								$valeurTestModif = "Nombre de charactères incorects."; 
							}
						
						// MODIFICATION
						if($erreur != 1) {

							$conn = OuvrirConnexion();
							$req = "UPDATE tdf_coureur set".$valeurInsertion." where n_coureur =".$_POST['numCoureur'];
							$reqTEST = "SELECT nom, prenom, code_tdf FROM vt_coureur WHERE nom = '".toSQL($nom)."' and prenom = '".toSQL($prenom)."' and code_tdf = '".$_POST['pays']."' ";
							
							$cur = preparerRequete($conn, $reqTEST);
							$tab = executerRequete($cur);

							$nbLignesBDD = oci_fetch_all($cur, $tab,0,-1,OCI_FETCHSTATEMENT_BY_ROW);

							if ($nbLignesBDD == 0) {
								$cur2 = preparerRequete($conn, $req);
								$tab = executerRequete($cur2);
								oci_commit($conn);
								$valeurTestModif = "Votre coureur a été modifié avec succès de la BDD.";
								$message = "\r\n\r\nModification avec succès du coureur $nom $prenom \r\n$req\r\n\r\n";
								traceLog($fp, $message);
							}
							else {
								$erreur = 1;
								$valeurTestModif = "Votre coureur existe déjà.";
								$message = "\r\n\r\nEchec\r\n$req\r\n\r\n";
								traceLog($fp, $message);
							}
							
							$valeurTestSelectionCoureur = "";
							$valeurTestNom = "";
							$nom = "";
							$valeurNumCoureur = "";
							$prenom = "";
							$valeurTestPrenom = "";
							$valeurAnnee = "";
							$valeurParticipation = "";
							$valeurPays = "";

							FermerConnexion($conn);
						}
						
					}
					else {
							$valeurTestSelectionCoureur = "Veuillez au moins changer un des champs pour modifier le coureur.";
							$valeurNumCoureur = $_POST['numCoureur'];
						}
				}
				else{
					$valeurTestSelectionCoureur = "Aucun coureur sélectionné.";
				}
			}
		?>
		
		<form name="formModCoureur" action="" method="post" >
			<div align="center" style="margin-left:10%; margin-right:10%">
				
				<fieldset >
					<table border=0 cellpadding=10>
						<tr>
							<td>
								Choisissez le coureur à modifier :
							</td>
							<td>
								<?php
									$conn = OuvrirConnexion();
									$req = 'SELECT n_coureur, nom, prenom from tdf_coureur where n_coureur > 0 order by nom';
									$cur = preparerRequete($conn, $req);
									$tab = executerRequete($cur);
									$nbLignes = oci_fetch_all($cur, $tab,0,-1,OCI_FETCHSTATEMENT_BY_ROW);
									
									echo "<select name='numCoureur' size=1>";
										echo "<option value='Selectionnez un coureur'>Selectionnez un coureur</option>";
										for ($i=0;$i<$nbLignes;$i++) {
											echo '<option value="'.$tab[$i]["N_COUREUR"].'">'.$tab[$i]["NOM"]." ".utf8_encode($tab[$i]["PRENOM"]);
											echo '</option>';
										} 	
									echo "</select> ";
									FermerConnexion($conn);
								?>
							</td>
							<td>
								<font color='red'><?php echo $valeurTestSelectionCoureur; ?></font>
							</td>
						</tr>
						<tr>
							<td>
								Nom<sup>*</sup> :
							</td>
							<td>
								<input type="text" name="nom" size=32 maxlength=20 value="<?php if(isset($nom)) echo $nom; ?>" > 
							</td>
							<td>
								<font color="red" size=2><b><?php echo $valeurTestNom; ?></b></font>
							</td>
						</tr>
						<tr>
							<td>
								Prenom<sup>*</sup> :
							</td>
							<td>
								<input type="text" name="prenom" size=32 maxlength=30 value="<?php if(isset($prenom)) echo $prenom; ?>" >
							</td>
							<td>
								<font color="red" size=2><b><?php echo $valeurTestPrenom; ?></b></font>
							</td>
						</tr>
						<tr>
							<td>
								Année de naissance<sup>*</sup> :
							</td>
							<td>
								<select name="annee_naissance" size=1>
									<?php 
									echo "<option value=''>Année de naissance</option>";
									for($i=1997; $i>1949; $i--) {
										if($i == $valeurAnnee) {
											echo "<option value=".$i." selected>".$i."</option>";
										}
										else {
											echo "<option value=".$i.">".$i."</option>";
										}
									}
									?>
								</select>
							</td>
							<td>
								<font color="red" size=2><b><?php echo $valeurTestAnneeNaissance; ?></b></font>
							</td>
						</tr>
						<tr>
							<td>
								Année de premier tour de France :
							</td>
							<td>
								<select name="annee_tour" size=1>
									<?php 
									echo "<option value=''>Année de la première participation</option>";
									for($i=2015;$i>1950;$i--) {
										if($i == $valeurParticipation) {
											echo "<option value=".$i." selected>".$i."</option>";
										}
										else {
											echo "<option value=".$i.">".$i."</option>";
										}
									}
									?>
								</select>
							</td>
							<td>
							</td>
						</tr>
						<tr>
							<td>
								Pays<sup>*</sup> :
							</td>
							<td>
								<?php

									$conn = OuvrirConnexion();
									$req = 'SELECT code_tdf, c_pays, nom from TDF_PAYS where nom not like \'-%\' order by nom';
									$cur = preparerRequete($conn, $req);
									$tab = executerRequete($cur);
									FermerConnexion($conn);
									$nbLignes = oci_fetch_all($cur, $tab,0,-1,OCI_FETCHSTATEMENT_BY_ROW);
									
									echo "<select name='pays' size=1>";
										echo "<option value='Selectionnez un pays'>Selectionnez un pays</option>";
										for ($i=0;$i<$nbLignes;$i++) {
											if($tab[$i]["CODE_TDF"] == $valeurPays ){
												echo '<option value="'.$tab[$i]["CODE_TDF"].'" selected>'.$tab[$i]["NOM"];
											}
											else {
												echo '<option value="'.$tab[$i]["CODE_TDF"].'">'.$tab[$i]["NOM"];
											}
										  echo '</option>';
										} 	
									echo "</select> ";
									
								?>	
							</td>
							<td>
								<font color='red' size=2><b><?php echo $valeurTestPays; ?></b></font>
							</td>
						</tr>
						<tr>
							<td>
								<font size=1><sup>*</sup>Champs obligatoires</font>
							</td>
							<td align="center">
								<input type='submit' name='Modifier' value='Modifier le coureur' >
							</td>
							<td>
								<font color='green' size=2><b><?php echo $valeurTestModif; ?></b></font>
							</td>
						</tr>
					</table>
					
				</fieldset>
			</div>
		</form>

	</body>
</html>