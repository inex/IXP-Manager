$emailText = "Hi,\n\n";
$emailText .= "You or someone in your organisation requested details on the following cross connect to ".env( 'IDENTITY_ORGNAME' ).".\n\n";
$emailText .= "Colo Reference: ".$ppp->getColoCircuitRef()."\n";
$emailText .= "Patch panel: ".$ppp->getPatchPanel()->getName()."\n";
$emailText .= "Port: ".$ppp->getName()."\n";
$emailText .= "State: ".$ppp->resolveStates()."\n";

if( $ppp->getCeaseRequestedAt() ){
$emailText .= "Cease requested: ".$ppp->getCeaseRequestedAtFormated()."\n";
}

$emailText .= "Connected on: ".$ppp->getConnectedAtFormated()."\n\n";

if( $ppp->hasPublicFiles() ){
$emailText .= "We have attached documentation which we have on file regarding this connection.\n\n";
}

if( $ppp->getNotes() ){
$emailText .= "We have also recorded the following notes:\n\n";
$emailText .= $ppp->getNotes()."\n\n";
}

$emailText .= "> add with leading '>' so it appears quoted\n\n";
$emailText .= "If you have any queries about this, please reply to this email.\n\n";
