<?php 



#Affichage erreurs ; 
ini_set("display_errors", 1);

#Ajout des fonctions ;
include('fonctions.php');
include('php_mail.php');

/* Lecture du fichier */
# $tab = données du questionnaires brutes ; 
$tab = openCsv("data_ano",",");  
#print_df($tab);



/* Traitement des données */
/*			Partie Transformation des coéfficients 		*/		
#execute function ; 
$tab = cleanData($tab); 
#Vérification des résultats ; 
#print_df($tab);

try{
	$df = createDf($tab); 
} catch (Exception $e){
	echo 'Exception reçue : ',  $e->getMessage(), "\n";
}
$df = array_map('array_values', $df); #reset de l'index suite à la suppression de certaines valeurs; 
/* Export pour vérification sur Excel */
// try{
//  exportCsv($df,"coefficients_pour_corr");
// } catch (Exception $e) {
// 	echo 'Exception reçue : ',  $e->getMessage(), "\n";
// }


/* Bug décalage du calcul des coefficients */ 
#$testSum = array(2,	5,	5,	5,	3,	5,	5,	3,	4,	4,	4,	5,	5,	2,	3,	5,	5,	2,	5,	5,	1,	3,	5,	5,	1,	4,	3,	4,	4,	4,	5,	2,	4);
#$testSum2 = array(2,	4,	5,	5,	4,	5,	5,	3,	4,	5,	5,	4,	5,	3,	5,	5,	5,	4,	5,	5,	4,	5,	4,	5,	5,	5,	4,	5,	5,	1,	5,	3,	5);
#$testSum = addition($testSum,$testSum2);
#$testCorr = Corr($testSum,$testSum2);
#print_df($testSum);
#$somme = createMatrixSum($df);
#print_df($somme);

  
/* Création de la matrice de corrélation*/
try{
	$matrix_corr = createMatrixCorr($df); 
} catch (Exception $e) {
    echo 'Exception reçue : ',  $e->getMessage(), "\n";
}
/* Affichage */
#print_df($matrix_corr);

/* Export */ 
// try{
// 	exportCsv($matrix_corr,"matrice_des_corrélations");
// }catch (Exception $e) {
//     echo 'Exception reçue : ',  $e->getMessage(), "\n";
// }



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


$recommandations = finalMatrix($tab,$slice); 
#print_df($recommandations); 
/* Export */ 
// exportCsv($recommandations,"matrice_finale");


#$test = getReco($tab,$recommandations,'hos.fabien@outlook.com'); 
#print_df($test);

/* Récupérer les adresses mails */
$lstMail = getListMail($tab); 
#print_df($lstMail);


#Pas nécessaire finalement ; 
#$listeEnvoi = createListeEnvoi($lstMail,$tab,$recommandations); 
#print_df($listeEnvoi);
#print_df($tab);




#Récupère le contenu du formulaire html ; 
$var = strtolower($_POST['mail']);

#initialisation des variables pour éviter des erreurs ; 
$reponse = "Votre adresse n'a pas été retrouvé dans notre bdd";
$reponse2 = '' ;  

if (in_array($var, $lstMail))
{
	$reponse = getReco($tab,$recommandations,$var); 
	$reponse2 = getHoro($tab,$var); 
}

// for($i=0; $i < $lstMail; $i++)
// {
// 	if (isset($lstMail[$i]))
// 	{
// 		mailSend($lstMail[$i],$reponse);
// 	}
//   }

	

if (in_array($var,$lstMail)){
echo "
<meta charset='utf-8'>
    <link rel='stylesheet' href='css/style.css'>
    <link rel='preconnect' href='https://fonts.googleapis.com'>
    <link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>
    <link href='https://fonts.googleapis.com/css2?family=Roboto&display=swap' rel='stylesheet'>
 <body>
    <article>
    <section>
    <h1>Vos recommandations </h1>
    <p>Bonjour <b>".$var."</b> merci d'avoir participé à ce questionnaire le ".$reponse2." vos recommandations sont les suivantes :</p>
    <table>
    <tr>
    <th><a href='https://www.google.com/search?q=vacances+".$reponse[0]."'target='_blank'</a>".$reponse[0]."</th>
    
    
    <th><a href='https://www.google.com/search?q=vacances+".$reponse[1]."'target='_blank'</a>".$reponse[1]."</th>
    
    
    <th><a href='https://www.google.com/search?q=vacances+".$reponse[2]."'target='_blank'</a>".$reponse[2]."</th>
  
    </table>

    <p id='middle'> Nous sommes curieux de connaître la pertinence des recommandations que vous avez reçu, n'hésitez pas à nous recontacter à l'adresse : <b>hos.fabien@outlook.com</b> / <b>yasminedarwish@outlook.fr</b> pour nous faire part de votre avis. </p>
    <p > Pour rappel, ces recommandations sont basées sur la participation des autres utilisateurs, il se peut que l'on vous  recommande la même destination que vous aviez partagé car un autre utilisateur l'a fait, lui aussi ! </p>
    <p id='end'> Le code du projet est disponible <a href='https://github.com/surybang/USID08_PHP_PROJECT/'> ici</a></p>
    <a href = 'https://formation.cnam.fr/rechercher-par-discipline/master-mega-donnees-et-analyse-sociale-medas--1085595.kjsp' target='_blank' </a> <img src='img/CNAM_Logo.svg.png' alt='logo cnam'>

    </section>
    
  
    </article>


  </body>

";
}else {
	echo "
	<meta charset='utf-8'>
    <link rel='stylesheet' href='css/style.css'>
    <link rel='preconnect' href='https://fonts.googleapis.com'>
    <link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>
    <link href='https://fonts.googleapis.com/css2?family=Roboto&display=swap' rel='stylesheet'>
 <body>
    <article>
    <section>
    <h1>Adresse mail non reconnue</h1>
    <p>Bonjour <b>".$var."</b> merci de remplir <a href = 'https://docs.google.com/forms/d/1Jsj4RzD522fPebKaaCMOqOcKUvm2egKqyvcTTctMKTY' target='_blank'>le formulaire </a>, nous n'avons pas pu retrouver vos résultats dans nos données.</p>
    
    <a href = 'https://formation.cnam.fr/rechercher-par-discipline/master-mega-donnees-et-analyse-sociale-medas--1085595.kjsp' target='_blank' </a> <img src='img/CNAM_Logo.svg.png' alt='logo cnam'>

    </section>
    
  
    </article>


  </body>

";
}






?>
