

<?php 
ini_set("display_errors", 1);
# utilisation de sendmail ; 
# https://www.youtube.com/watch?v=Fywr8gIVdLY ;



function mailSend($adr,$reco){
    if (isset($adr))
    {
        $to = $adr;
        $subject = "Vos recommandations suite au questionnaire" ;
        $message = "Bonjour ".$adr." , <br> Vous avez répondu à notre questionnaire concernant votre prochaine destination en France, nous vous proposons de le découvrir en vous rendant sur notre site web dédié <a href='https://projet-medas-destination-france.000webhostapp.com' target = '_blank'> https://projet-medas-destination-france.000webhostapp.com </a> <br> Bien cordialement, <br> YOUR NAME" ;
        $headers = "Content-type : text/plain ; charset = utf8 \r\n";
        $headers .= "From: YOUR NAME <YOUR@ADDRESS.com> \r\n"; 
        if(mail($to, $subject, $message,$headers))
        {
            echo 'envoye';
        }else
        {
        echo'erreur';
        }
    }

}



?>

