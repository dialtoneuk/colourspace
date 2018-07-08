<?php
require_once "vendor/autoload.php";

define("CMD", true );

include_once "index.php";

echo( "Colourspace Resource Unpacker \n");

try
{

    echo( " - Starting \n");
    $unpacker = new \Colourspace\Framework\Util\ResourceUnpacker();
    $unpacker->process();
    echo( " - Files created \n");
    unlink( COLOURSPACE_ROOT . RESOURCE_COMBINER_FILEPATH );
    echo( " - Deleted Resource File \n");
}
catch( Error $error )
{

    echo( "Critical Error: " . $error->getMessage() . "\n"  );
}

echo("Finished");