<?php 


#Affichage erreurs ; 
ini_set("display_errors", 1);


/* Lecture du fichier */

$tab = array();
$fichier = fopen("data_good.csv","r");
while($ligne = fgetcsv($fichier,1024,",")){
	array_push($tab,$ligne);
}
fclose($fichier);

$df = array(); 


/* Affichage du fichier */

// print('<pre>');
// print_r($tab);
// print('</pre');
#print(count($tab)-1);  # nombre de lignes dans le fichier ; 


 




/* Traitement des données */

# L'email & la recommandation ; 
# Ces données ne doivent pas être prise en compte par notre fonction des corrélations  ; 
# === > On ajuste les i et les j dans la boucle ; 



# Conversion des valeurs quali en quanti avec échelle de Lickert ; 
# J'adore 				|| Tout à fait d'accord 	= 5 
# J'aime bien 			|| D'accord					= 4 
# Bof 					|| Ne sait pas  			= 3 
# Je n'aime pas trop 	|| Pas d'accord				= 2 
# Je n'aime pas du tout || Pas du tout d'accord 	= 1 

#modalités de var âge : 
# <  18 ans =  1
# 18-25 ans =  2
# 26-35 ans =  3
# 36-45 ans =  4
# >  45 ans =  5 


#modalités de var sexe :  mettre en binaire ??? 
# homme  = 1 
# femmme = 2 
# autre  = 3  

/*			Partie Transformation des coéfficients 		*/		
#print_r($tab[0]); # columns ; 
#print_r($tab[1]); # first row , tableau associatif ? ; 
#print_r($tab[1][37]); # email de la première ligne ; 

# je cherche le premier j'adore ; 
# il faut réussir à boucler pour automatiser ce traitement ; 

#print_r($tab[1][]);
// if($tab[1][4] == "J'adore"){
// 	$tab[1][4] = 5 ; 
// }
#print_r($tab[1][4]);
#print_r($tab[1]);


# le sexe peut biaiser la moyenne lors du calcul des corr Pearson ? ; 


#	Solution maison -> double boucle for , foreach ne serait pas mieux ?  ; 
	
for($i=1; $i < count($tab) ; $i++ ){ 
	#print_r("i: ".$i."\n"); #debug 
	for($j=1; $j < 37 ; $j++){  # Il faudrait réussir à trouver le nombre de colonnes automatiquement  
	#	print_r("j: ".$j."\n"); #debug 
	#	print_r($tab[$i][$j]); #debug 
		if ($tab[$i][$j] == "J'adore" or $tab[$i][$j] == "Tout à fait d'accord" or $tab[$i][$j] == "46 et plus"){
			$tab[$i][$j] = 5 ; 
			#print("test"); #debug
		}
		elseif ($tab[$i][$j] == "J'aime bien" or $tab[$i][$j] == "D'accord" or $tab[$i][$j] == "36-45" ){
			$tab[$i][$j] = 4 ; 
		}
		elseif ($tab[$i][$j] == "Bof" or $tab[$i][$j] == "Ne sait pas" or $tab[$i][$j] == "26-35"){
			$tab[$i][$j] = 3 ; 
		}
		elseif ($tab[$i][$j] == "Je n'aime pas trop" or $tab[$i][$j] == "Pas d'accord" or $tab[$i][$j] == 'Femme' or $tab[$i][$j] == "18-25"){
			$tab[$i][$j] = 2 ; 
		}
		elseif ($tab[$i][$j] == "Je n'aime pas du tout" or $tab[$i][$j] == "Pas du tout d'accord" or $tab[$i][$j] == "Homme" or $tab[$i][$j] == "Moins de 18 ans"){
			$tab[$i][$j] = 1 ;  ## mettre le sexe en binaire ? ; 
		}
	}
} 
#Vérification des résultats ; 
   #print('<pre>');
   #print_r($tab);
   #print('</pre');


/* Création du df */ 
$df = $tab;  # on fait une copie de tab pour pouvoir isoler les coefficients ; 
for($i=0; $i < count($df); $i++){
	for($j=0; $j <= 36 ; $j++){
		if ($j == 0 or $j == 35 or $j == 36 ){
			unset($df[$i][$j]);
		}
	}
	#echo "<pre>";
	#print_r($df[$i]);
	#echo "</pre>";
}
  
/* Fonction des corrélations */ 

# Cette fonction doit nous renvoyer le coefficient de corrélations 
# entre deux arrays ( deux individus ) mais pour 1 individu il faut 
# lancer la fonction n fois jusqu'à trouver le max des coefficients 
# La meilleure option serait de créer une matrice des corrélations
# Pour ensuite trouver le max dans l'array ; 


function Corr($x, $y){

	$length= count($x);
	$mean1=array_sum($x) / $length;
	$mean2=array_sum($y) / $length;

	$a=0;
	$b=0;
	$axb=0;
	$a2=0;
	$b2=0;

	for($i=1;$i<$length;$i++)
	{
		$a=$x[$i]-$mean1;
		$b=$y[$i]-$mean2;
		$axb=$axb+($a*$b);
		$a2=$a2+ pow($a,2);
		$b2=$b2+ pow($b,2);
	}

	$corr= $axb / sqrt($a2*$b2);

	return $corr;
}

 #print(Corr([1,2,3,4],[3,2,3,5])); # WORK
 #print(Corr([1,2,3,4],[3,2,3,"ne sait pas"])); # ERROR 500 


#print_r(Corr($df[1], $df[2]));
$df_corr = array() ; 
$val = array() ; 
for($i=1;$i <= 18 ; $i++ ){
 	  for($j=1; $j < 18 ; $j++){
 	  	#echo "<pre>";
 	  	$res = Corr($df[$i],$df[$j]);
 	  	array_push($val,$res); ## trouver le moyen de changer d'indice quand j == 18 ; 
 	  	#print_r("i : ".$i.' la corrélation avec '.$j.' est égale à : '.Corr($df[$i],$df[$j]));
 	  	#echo "</pre>";
 	  }
 	  // print_r($df[$i]);
 	  // print_r($df[$j]);
 	}
print_r($val);
print_r($df_corr);

// $tableau = array() ; 
 #for($i=0;$i <= 2 ; $i++ ){
 	 // for($j=3; $j < 37 ; $j++){
 	 #$output = array_slice($tab[$i], 1, 34);
 	 # récupérer les valeurs de output dans une liste classique 
 	 #}
 // }
 	#print('<pre>');
 	#print_r($output);
 	#print('</pre>');
 // for($i=1;$i <= 3 ; $i++ ){  
	
	// for($j=2; $j < 3 ; $j++){
 // 	 	Corr($tab[$i],$tab[$j]); 
 // 	}
 // }
## erreur boucle car on lit un string ? ; 



/* 			Partie envoie de l'email 							*/ 
# Inclure le fichier php_mail.php ; 
# TODO : Boucler quand columns(j)==37 pour récupérer l'adresse mail et envoyer la réponse 
# La réponse = individu avec max corrélation et j == 36 ; 









 




















?>