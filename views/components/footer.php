<footer>

    <?php
    if( isset( $model['footer'] ) && empty( $model['footer'] ) == false )
        foreach( $model['footer'] as $script )
            echo( "<script type='text/javascript' src='$script'></script>")
    ?>
</footer>