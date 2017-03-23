
$emailText = "Hi,\n\n";
$emailText .= "You or someone in your organisation requested details on the following cross connect to ".env( 'IDENTITY_ORGNAME' ).".\n\n";
$emailText .= "Colo Reference: ".$ppp->getColoCircuitRef()."\n";
$emailText .= "Patch panel: ".$ppp->getPatchPanel()->getName()."\n";
$emailText .= "Port: ".$ppp->getName()."\n";
$emailText .= "State: ".$ppp->resolveStates()."\n\n";
$emailText .= "We have attached the Letter of Agency in PDF format.\n\n";
$emailText .= "> add with leading '>' so it appears quoted\n\n";
$emailText .= "If you have any queries about this, please reply to this email.\n\n";
