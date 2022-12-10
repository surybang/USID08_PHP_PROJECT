<?php

function openCsv($name,$delimiter){
	$tab = array();
	$fichier = fopen($name.".csv","r");
	while($ligne = fgetcsv($fichier,1024,$delimiter))
	{
		array_push($tab,$ligne);
	}
	fclose($fichier);
	return $tab;
}

function cleanData($tab) {
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

	for($i=1; $i < count($tab) ; $i++ )
	{ 
	#print_r("i: ".$i."\n"); #debug 
		for($j=1; $j < 37 ; $j++)
		{  
			if ($tab[$i][$j] == "J'adore" or $tab[$i][$j] == "Tout à fait d'accord" or $tab[$i][$j] == "46 et plus")
			{
				$tab[$i][$j] = 5 ; 
			}
			elseif ($tab[$i][$j] == "J'aime bien" or $tab[$i][$j] == "D'accord" or $tab[$i][$j] == "36-45" )
			{
				$tab[$i][$j] = 4 ; 
			}
			elseif ($tab[$i][$j] == "Bof" or $tab[$i][$j] == "Ne sait pas" or $tab[$i][$j] == "26-35")
			{
				$tab[$i][$j] = 3 ; 
			}
			elseif ($tab[$i][$j] == "Je n'aime pas trop" or $tab[$i][$j] == "Pas d'accord"  or $tab[$i][$j] == "18-25")
			{
				$tab[$i][$j] = 2 ; 
			}
			elseif ($tab[$i][$j] == "Je n'aime pas du tout" or $tab[$i][$j] == "Pas du tout d'accord" or $tab[$i][$j] == "Moins de 18 ans")
			{
				$tab[$i][$j] = 1 ;   
			}
		}
	} 
	return $tab; 
} 




/* Affichage du fichier */
Function print_df($x){
	print('<pre>');
 	print_r($x);
 	print('</pre>');
}

/* Exporter une table au format .csv et choisir le nom */ 
function exportCsv($data,$name){
	$fp = fopen($name.'.csv', 'w');
	if (isset($data)){
		foreach ($data as $fields) 
		{
    		fputcsv($fp, $fields);
		}

		fclose($fp);

	}
	else {
		throw new Exception ("données vide"); 
	}
	

}
 /* Fonction des corrélations Pearson */ 
 function Corr($x, $y){

	$length= count($x);  # x et y font la même taille donc pas besoin de définir 2 tailles diff 
	$mean1=array_sum($x) / $length; # somme des valeurs dans la liste $x / taille 
	$mean2=array_sum($y) / $length;

	$a=0;
	$b=0;
	$axb=0;
	$a2=0;
	$b2=0;

	for($i=0;$i<=$length-1;$i++)
	{
		if (is_int($x[$i]) or is_int($y[$i]))
		{
			 
			$a=$x[$i]-$mean1;
			$b=$y[$i]-$mean2;
			$axb=$axb+($a*$b);
			$a2=$a2+ pow($a,2);
			$b2=$b2+ pow($b,2);
		}
		else {
			throw new Exception('x ou y doivent être numérique'); 
		}
	}

	$corr= $axb / sqrt($a2*$b2);

	return $corr;
}

function Descending($a, $b) {   
    if ($a == $b) {        
        return 0;
    }   
        return ($a > $b) ? -1 : 1; 
}  

/* Création du df */ 
function createDf($data){
	$df = array(); 
	$df = $data;  # on fait une copie de tab pour pouvoir isoler les coefficients et garder le fichier de données à l'état brut ; 

	if (isset($data))
	{
		for($i=0; $i < count($df); $i++)
		{
			for($j=0; $j <= 36 ; $j++)
			{
				if ($j == 0 or $j==2 or $j == 35 or $j == 36 )
				{
					unset($df[$i][$j]);
				}
			}
			#DEBUG 
			#echo "<pre>";
			#print_r($df[$i]);
			#echo "</pre>";
		}

	}else{
		throw new Exception ('pas de données'); 
	}
	

	return $df; 

}

// function addition($x,$y){
// 	$x1=array_sum($x);
// 	$y1=array_sum($y);
// 	$sum = $x1 + $y1 ;
// 	return $sum;
// }


function createMatrixCorr($data)
{
	$val = array() ; 
	$n = count($data)-1;
	for($i=1;$i < count($data) ; $i++)
 	{
  	  for($j=1; $j < count($data) ; $j++) # - 1 on skip les titres 
  	  {
  	  	$res = Corr($data[$i],$data[$j]);
  	  	array_push($val,$res);  
  	  	#echo "<pre>";
  	  	#print_r("i : ".$i.' la corrélation avec '.$j.' est égale à : '.Corr($data[$i],$data[$j]));
  	  	#echo "</pre>";
  	  }

  }

#on casse l'array $val toutes les n valeurs pour refaire un array ; 
	$matrix_corr = array_chunk($val,$n);

return $matrix_corr; 

}


// function createMatrixSum($data)
// {
// 	$val = array() ; 
// 	$n = count($data)-1;
// 	for($i=1;$i < count($data) ; $i++)
//  	{
//   	  for($j=1; $j < count($data) ; $j++) # - 1 on skip les titres 
//   	  {
//   	  	$res = Corr($data[$i],$data[$j]);
//   	  	#array_push($val,$res);  
//   	  	#echo "<pre>";
//   	  	#print_r("i : ".$i.' la somme avec '.$j.' est égale à : '.addition($data[$i],$data[$j]));
//   	  	#echo "</pre>";
//   	  }

//   }

// #on casse l'array $val toutes les n valeurs pour refaire un array ; 
// 	$matrix_sum = array_chunk($val,$n);

// return $matrix_sum; 

// }

/* Tri des coefficients par ordre décroissant */
function OrderMatrix($x){
	for($i=0; $i < count($x); $i++)
	{
		for($j=0; $j < 37; $j++)  
		{
			if($x[$i][$j] == 1)  # on ne souhaite pas recommander ce que la personne a renseigné elle-même -> on modifie la val du coefficient ;
			{
				$x[$i][$j] = 0 ; 
			}
		}
	uasort($x[$i],"Descending");
	}
	return $x; 
}

/* Couper la matrice de corrélation avec les 3 plus grandes valeurs en gardant bien les clés */  
function sliceMatrix($x){
	$sliced_matrix_corr = array();  
	$res=array();
	foreach ($x as $slice_m) 
	{
    	$sliced_matrix_corr[] = array_slice($slice_m, 0, 3,true);
	}
// print_df($sliced_matrix_corr);

/* clés du tableau des corrélations */

	foreach($sliced_matrix_corr as $keys)
	{
		array_push($res,array_keys($keys));
	}
	return $res;

}

# on associe les clés des personnes corrélés avec la valeur correspondantes à ce qu'ils ont écrit dans le questionnaire ; 
function  finalMatrix($reponse,$slice){
	$recommandation = array();
	foreach($slice as $elem)
	{
		foreach($elem as $val)
		{
			if ($val == 0)  
			{
				array_push($recommandation,$reponse[1][35]);# +1 pour éviter les en-têtes quand $val == 0;
			}
			else
			{
				array_push($recommandation,$reponse[$val][35]);
			}

		
		}
	}
	$recommandation = array_chunk($recommandation,3); # on sait que tt les 3 elem on switch de personne dans le tableau ; 
	return $recommandation; 

}

/* Récupérer les recommandations personnalisés pour 1 participant */ 
function getReco($reponse,$matrice,$email){
	for($i=0;$i<count($reponse)-1;$i++)
	{
		for($j=1;$j<37;$j++)
		{
			if (strtolower($reponse[$i][$j]) == strtolower($email)) # on utilise l'email comme identifiant 
			{
				$reco = $matrice[$i];
			}
		} 
	}
	return $reco ; 
}

/* Récupérer la colonne horodatage */ 
function getHoro($reponse,$email){
	for($i=0;$i<count($reponse);$i++)
	{
		for($j=1;$j<37;$j++)
		{
			if (strtolower($reponse[$i][$j]) == strtolower($email))
				{
					$horo = $reponse[$i][0];
				}	
		} 
	}
	return $horo ; 
}



/* Récupérer la liste des adresses mails dans le fichier de base */ 
function getListMail($tab) {
	$lstMail=array();
	
	for($i=1;$i<count($tab);$i++)
	{
		array_push($lstMail,strtolower($tab[$i][36]));
	}
	return $lstMail;
}

/* Créer la liste de diffusion des mails avec les recommandations pour chaque adresse */ 
// function createListeEnvoi($lstMail,$tab,$recommandations){
// 	$listeEnvoi=array();
// 	for($i=0;$i<count($lstMail)-1;$i++)
// 	{
// 		if (isset($tab[$i][36])){
// 			array_push($listeEnvoi,getReco($tab,$recommandations,$lstMail[$i]));
// 		}
// 	}
// 	return $listeEnvoi ;
// }



// // [START drive_download_file]
// use Google\Client;
// use Google\Service\Drive;
// function downloadFile()
//  {
//     try {

//       $client = new Client();
//       $client->useApplicationDefaultCredentials();
//       $client->addScope(Drive::DRIVE);
//       $driveService = new Drive($client);
//       $realFileId = readline("Enter File Id: ");
//       $fileId = '0BwwA4oUTeiV1UVNwOHItT0xfa2M';
//       $fileId = $realFileId;
//       $response = $driveService->files->get($fileId, array(
//           'alt' => 'media'));
//       $content = $response->getBody()->getContents();
//       return $content;

//     } catch(Exception $e) {
//       echo "Error Message: ".$e;
//     }
   
// }
// // [END drive_download_file]
// require_once 'vendor/autoload.php';
// downloadFile();


// function exceptions_error_handler($severity, $message, $filename, $lineno) {
//     throw new ErrorException($message, 0, $severity, $filename, $lineno);
// }

// set_error_handler('exceptions_error_handler');

	

?>
