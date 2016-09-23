<?php

// You'll want to allow access from some mgmt IPs during your upgrade:

$safe_ips = [
    '127.0.0.1',
    '::1',
    // add more as you need
];

if( !in_array( $_SERVER['REMOTE_ADDR'], $safe_ips ) ) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>IXP Manager - Under Maintenance</title>

    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0-rc1/css/bootstrap.min.css" rel="stylesheet">

    
    <style>
	/* Sticky footer styles
	-------------------------------------------------- */
	
	html,
	body {
	  height: 100%;
	  /* The html and body elements cannot have any padding or margin. */
	}
	
	/* Wrapper for page content to push down footer */
	#wrap {
	  min-height: 100%;
	  height: auto !important;
	  height: 100%;
	  /* Negative indent footer by its height */
	  margin: 0 auto -60px;
	  /* Pad bottom by footer height */
	  padding: 0 0 60px;
	}
	
	/* Set the fixed height of the footer here */
	#footer {
	  height: 60px;
	  background-color: #f5f5f5;
	}
	
	/* Lastly, apply responsive CSS fixes as necessary */
	@media (max-width: 767px) {
	  #footer {
	    margin-left: -20px;
	    margin-right: -20px;
	    padding-left: 20px;
	    padding-right: 20px;
	  }
	}
	
	/* Custom page CSS
	-------------------------------------------------- */
	/* Not required for template or sticky footer method. */
			
	.container {
	  width: auto;
	  max-width: 680px;
	  padding: 0 15px;
	}
	.container .credit {
	  margin: 20px 0;
	}
    </style>
  </head>

  <body>

    <!-- Wrap all page content here -->
    <div id="wrap">

      <!-- Begin page content -->
      <div class="container">
        <div class="page-header">
          <h1>IXP Manager - Under Maintenance</h1>
        </div>
        <p class="lead">We are currently in the process of updating IXP Manager.</p>
        <p>This is normally a quick process and we should be back on line in a few minutes.</p>
      </div>
    </div>

    <div id="footer">
      <div class="container">
        <a href="https://github.com/inex/IXP-Manager">IXP Manager</a> - Copyright &copy; 2010 - <?php echo date( 'Y' ); ?> <a href="https://www.inex.ie/">Internet Neutral Exchange Association Company Limited By Guarantee.</a>
        <!-- p class="text-muted credit">Example courtesy <a href="http://martinbean.co.uk">Martin Bean</a> and <a href="http://ryanfait.com/sticky-footer/">Ryan Fait</a>.</p -->
      </div>
    </div>

  </body>
</html>
<?php
    // continuing if( !in_array( $_SERVER['REMOTE_ADDR'], $safe_ips ) ) {
    // we need to die now as we're just displaying the under maintenance page
    die();

} // if( !in_array( $_SERVER['REMOTE_ADDR'], $safe_ips ) )
