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

    $combiner = new \Colourspace\Framework\Util\ResourceCombiner(RESOURCE_COMBINER_ROOT );

    $build = $combiner->build();

    if( empty( $build ) )
        throw new Error("Build returned null \n");

    $combiner->save( $build );

    echo("Complete!");
}
catch( Error $error )
{

    echo( "ERROR:" . $error->getMessage() );
}
