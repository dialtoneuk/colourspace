<head>

    <!--TODO: Custom Page Titles-->
    <title>Colourspace</title>

    <?php


        if( isset( $content->header ) && empty( $content->header ) == false )
            foreach( $content->header as $script )
                echo( "<script type='text/javascript' src='$script'></script>")
    ?>
</head>