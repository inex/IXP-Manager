$emailText = "Hi,\n\n";
$emailText .= "** ACTION REQUIRED - PLEASE SEE BELOW **\n\n";
$emailText .= "You have a cross connect to ".env( 'IDENTITY_ORGNAME' )." which our records indicate is no longer required.\n\n";
$emailText .= "Please contact the co-location facility and request that they cease the following cross connect:\n\n";
$emailText .= "Colo Reference: ".$ppp->getColoCircuitRef()."\n";
$emailText .= "Patch panel: ".$ppp->getPatchPanel()->getName()."\n";
$emailText .= "Port: ".$ppp->getName()."\n";
$emailText .= "Connected on: ".$ppp->getConnectedAtFormated()."\n\n";

if( $ppp->hasPublicFiles() ){
$emailText .= "We have attached documentation which we have on file regarding this connection which may help process this request.\n\n";
}

if( $ppp->getNotes() ){
$emailText .= "We have also recorded the following notes which may also be of use:\n";
$emailText .= $ppp->getNotes()."\n\n";
}

$emailText .= "> add with leading '>' so it appears quoted\n\n";
$emailText .= "If you have any queries about this, please reply to this email.\n\n";
