<div class="row">
    <?php
        if( empty( $content->errors ) == false )
        {
            $errors = json_decode ( json_encode( $content->errors ) , true);

            foreach(  $errors as $key=>$error )
                echo("<p>" . $error['type'] . " : " . $error['value'] . "</p>" );
        }

        if( empty( $content->messages ) == false )
        {
            $messages = json_decode ( json_encode( $content->messages ), true);

            foreach(  $errors as $key=>$message )
                echo("<p>" . $message['type'] . " : " . $message['value'] . "</p>" );

        }
    ?>
</div>