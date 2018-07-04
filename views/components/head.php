<head>

    <!--TODO: Custom Page Titles-->
    <title>Colourspace</title>

    <?php


        if( isset( $model['header'] ) && empty( $model['header'] ) == false )
            foreach( $model['header'] as $script )
                echo( "<script type='text/javascript' src='$script'></script>")
    ?>
</head>