<?php 



#Affichage erreurs ; 
ini_set("display_errors", 1);

#Ajout des fonctions ;
include('fonctions.php');
include('php_mail.php');

/* Lecture du fichier */


# $tab = données du questionnaires brutes ; 
$tab = openCsv("datanew",",");  
#print_df($tab);



/* Traitement des données */
/*			Partie Transformation des coéfficients 		*/		
#execute function ; 
$tab = cleanData($tab); 
#Vérification des résultats ; 
#print_df($tab);

$df = createDf($tab); 
$df = array_map('array_values', $df); #reset de l'index suite à la suppression de certaines valeurs; 
#print_df($df);
/* Export pour vérification sur Excel */
// exportCsv($df,"coefficients_pour_corr");
  
/* Création de la matrice de corrélation*/
$matrix_corr = createMatrixCorr($df); 
/* Affichage */
 print_df($matrix_corr);

/* Export */ 
// exportCsv($matrix_corr,"matrice_des_corrélations");


/* Tri de la matrice par ordre décroissant */
$matrix_corr = OrderMatrix($matrix_corr); 
/* Affichage */
// print_df($matrix_corr); 

/* Couper la matrice de corrélation avec les 3 plus grandes valeurs en gardant bien les clés */  
$slice = sliceMatrix($matrix_corr);
/* Affichage */
// print_df($slice);



/* clés du questionnaires */
// $tableau2=array();
// foreach($tab as $keys){
// 	array_push($tableau2,array_keys($keys));
// }
#print_df($tableau2);

/* Si clé du tableau_corrélation == clé du tableau_questionnaire =>
on affiche le contenu de tableau_questionnaire[clé_tableau_corrélation][35] */


$recommandation = finalMatrix($tab,$slice); 
#print_df($recommandation); 
/* Export */ 
// exportCsv($recommandation,"matrice_finale");


#$test = getReco($tab,$recommandation,'thibaut.montagut@gmail.com'); 
#print_df($test);

/* Récupérer les adresses mails */
$lstMail=array();
for($i=1;$i<count($tab);$i++){
	array_push($lstMail,$tab[$i][36]);
}

#print_r($lstMail);


$listeEnvoi=array();
for($i=0;$i<count($lstMail)-1;$i++)
{
	if (isset($tab[$i][36])){
		array_push($listeEnvoi,getReco($tab,$recommandation,$lstMail[$i]));

	}

}
#print_df($listeEnvoi);
#print_df($tab);

$var = $_POST['mail'];
$test2 = "Votre adresse n'a pas été retrouvé dans notre bdd"; 
// for($i=0;$i<count($lstMail)-1;$i++){
// 	if($lstMail[$i] == $var ){
// 		$test2 = getReco($tab,$recommandation,$var); 
// 	}}
if (in_array($var, $lstMail)){
	$test2 = getReco($tab,$recommandation,$var); 
	$test3 = getHoro($tab,$var); 
}




print_df($test2); 


print("Bonjour ".$var." merci d'avoir participé à ce questionnaire le ".$test3." tes recommandations sont les suivantes : ");
echo "<br>";
print('1er  choix : '.$test2[0]); 
echo "<br>";
print('2eme choix : '.$test2[1]); 
echo "<br>";
print('3eme choix : '.$test2[2]); 
echo "<br>";

/* 			Partie envoie de l'email 							*/ 
# Inclure le fichier php_mail.php ; 
# TODO : Boucler quand columns(j)==37 pour récupérer l'adresse mail et envoyer la réponse 
# La réponse = individu avec max corrélation et j == 36 ; 




?>
