<footer>

    <?php
    if( isset( $content->footer ) && empty( $content->footer ) == false )
        foreach( $content->footer as $script )
            echo( "<script type='text/javascript' src='$script'></script>")
    ?>
</footer>