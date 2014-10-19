<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;">
		<meta charset="utf-8">
		<title>Ajouter un directeur au Tour de France</title>
	</head>
	<body>

		<?php
			include_once ('../menuBarre.php');
			include_once ('../connBDD.php');
			include_once ('../fonc_test.php');
		?>

		<h2 align="center">Ajout d'un directeur</h2>

	<!-- FORMULAIRE POUR AJOUTER UN DIRECTEUR A LA BASE -->

	<?php
			
			// Initialisation des variables
			$erreur = 0;

			if(!isset($valeurTestNom)) {
				$valeurTestNom = "";
			}

			if(!isset($valeurTestPrenom)) {
				$valeurTestPrenom = "";
			}

			if(!isset($valeurTestAjout)) {
				$valeurTestAjout = "";
			}

			if(isset($_POST['Ajouter'])) {
				
				if($_POST['nom'] != "" && $_POST['prenom'] != "" ) {
					$erreur = 0;
					
					if(!testNom($_POST['nom'])) {
						$erreur = 1;
						$valeurTestNom = "Veuillez entrer un nom valide.";
					}
					else{
						$valeurNom = testNom($_POST['nom']);
						$nom = $valeurNom;
					}
					
					if(!testPrenom($_POST['prenom'])) {
						$erreur = 1;
						$valeurTestPrenom = "Veuillez entrer un prénom valide.";
					}
					else {
						$valeurPrenom = testPrenom($_POST['prenom']);
						$prenom = $valeurPrenom;
					}
					
					if($erreur != 1) {
						$conn = OuvrirConnexion();
						
						// On récupère le max du nmbre de directeur
						$reqMax = "SELECT max(n_directeur) AS MAXI FROM tdf_directeur";
						$cur = preparerRequete($conn, $reqMax);
						$tab = executerRequete($cur);
						$maxNumDirecteur = array();
						oci_fetch_all($cur,$maxNumDirecteur);
						$num=$maxNumDirecteur['MAXI'][0];
						// On souhaite que le nouveau directeur soit le max +1
						$num = $num + 1;
						
						$reqINS = "INSERT INTO TDF_DIRECTEUR(n_directeur, nom, prenom, compte_oracle, date_insert) values ($num,'".$nom."','".$prenom."', user, sysdate)";
						
						$cur = preparerRequete($conn, $reqINS);
						$tab = executerRequete($cur);
						oci_commit($conn);
						
						FermerConnexion($conn);
						
						$valeurTestAjout = "Directeur ajouté avec succès.";
						$valeurNom = "";
						$valeurPrenom= "";
					}
					
				}
				else{
					if($_POST['nom'] == "")
						$valeurTestNom = "Veuillez entrer un nom.";
					if($_POST['prenom'] == "")
						$valeurTestPrenom = "Veuillez entrer un prénom.";
					$valeurNom = $_POST['nom'];
					$valeurPrenom = $_POST['prenom'];
					
				}
			}
		?>
		
		<form name="formAddDirecteur" action="<?php $_SERVER['PHP_SELF'] ?>" method="post" >
			<div align="center" style="margin-left:20%; margin-right:20%">
				<fieldset >
					<table border=0 cellpadding=10>
						<tr>
							<td>
								Nom<sup>*</sup> :
							</td>
							<td>
								<input type="text" name="nom" size=40 value="<?php if(isset($valeurNom)) echo $valeurNom ?>" placeholder="Entrez un nom">
							</td>
							<td>
								<font color="red"><?php echo $valeurTestNom; ?></font>
							</td>
						</tr>
						<tr>
							<td>
								Prénom<sup>*</sup> :
							</td>
							<td>
								<input type="text" name="prenom" size=40 value="<?php if(isset($valeurPrenom)) echo $valeurPrenom; ?>" placeholder="Entrez un prénom"> 
							</td>
							<td>
								<font color="red"><?php if(isset($valeurTestPrenom)) echo $valeurTestPrenom; ?></font>
							</td>
						</tr>
						<tr>
							<td>
								<font size=1><sup>*</sup>Champs obligatoires</font>
								<br><p>Les homonymes sont acceptés.</p>
							</td>
							<td align="center">
								<input type='submit' name='Ajouter' value='Ajouter le directeur' >
								
							</td>
							<td>
								<font color='green'><?php echo $valeurTestAjout; ?></font>
							</td>
						</tr>
					</table>
					
				</fieldset>
			</div>
		</form>

	</body>
</html>