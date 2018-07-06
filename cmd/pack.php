<?php
require_once "vendor/autoload.php";

define("CMD", true ); //Scripts which run in CMD environment must define CMD to true.

include_once "index.php";

echo( "Colourspace Config Pack Script \n");

try
{

    echo( " - Starting \n");
    $combiner = new \Colourspace\Framework\Util\ResourceCombiner(RESOURCE_COMBINER_ROOT );
    $build = $combiner->build();

    if( empty( $build ) )
        throw new Error("Build returned null \n");

    echo( " - Files Packed \n");
    $combiner->save( $build );
    echo( " - Saved To File \n");
}
catch( Error $error )
{

    echo( "Critical Error:" . $error->getMessage() );
}

echo("Complete!");