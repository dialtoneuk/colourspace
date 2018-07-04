<div class="row">
    <?php
    if( empty( $model["errors"] ) == false )
    {
        foreach( $model["errors"] as $key=>$error )
        {

            echo("<p>" . $error['type'] . " : " . $error['value'] . "</p>" );
        }
    }

    if( empty( $model["messages"] ) == false )
    {

        foreach( $model["messages"] as $key=>$error )
        {

            echo("<p>" . $error['type'] . ":" . $error['value'] . "</p>" );
        }
    }
    ?>
</div>