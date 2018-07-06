<?php
require_once "../vendor/autoload.php";

/**
 * SCRIPTS WHICH ARE INVOKVED VIA CLI MUST DEFINE CMD ELSE THE APPLICATION WILL BOOT AS A WEB APPLICATION!
 */

define("CMD", true );

//We can then include the regular index, defining our needed globals for settings.
include_once "../index.php";

//We can then launch the softwares
echo( "Combining config files into one... \n");

try
{

    $unpacker = new \Colourspace\Framework\Util\ResourceUnpacker();

    print_r( $unpacker->get() );

    echo("Complete!");
}
catch( Error $error )
{

    echo( "ERROR:" . $error->getMessage() );
}
