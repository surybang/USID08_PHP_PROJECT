
<?php 
#ini_set("display_errors", 1);
# utilisation de sendmail ; 
# https://www.youtube.com/watch?v=Fywr8gIVdLY
#TODO : automatiser + traitement erreurs 
$to = "test@outlook.fr";
$subject = "test mail php" ;
$message = "Arrête de me répondre avec un 'non'";
$headers = "Content-type : text/plain ; charset = utf8 \r\n";
$headers .= "From: hos.fabien@outlook.fr \r\n"; 

if(mail($to, $subject, $message,$headers))
	echo 'envoye';
else 
	echo'erreur';

?>
