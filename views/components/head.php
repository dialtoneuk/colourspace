<head>

    <!--Test 2-->

    <!--TODO: Custom Page Titles-->
    <title>Colourspace</title>

    <!--Javascripts-->
    <?php
        if( isset( $content->header ) && empty( $content->header ) == false )
            foreach( $content->header as $script )
                echo( "<script type='text/javascript' src='$script'></script>")
    ?>

    <?php
        if( isset( $content->recaptcha ) && empty( $content->recaptcha ) == false )
        {

            echo( $content->recaptcha->script );
        }
    ?>
</head>