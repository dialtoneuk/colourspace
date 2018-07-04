<html>
    <?php
        Flight::render("components/head");
    ?>
    <body>
        <?php
            Flight::render("components/form_alerts");
        ?>
        <h1>
            Register
        </h1>
        <form method="POST">
            <?php
                if( isset( $content->temporaryusername ) && empty( $content->temporaryusername ) == false )
                {

                    ?>
                        Username
                        <input type="text" value="<?=$content->temporaryusername->username?>" name="username" disabled title="Username">
                    <?php
                }
                else
                {
                    ?>
                        Username
                        <input type="text" value="Random" id="username" name="username" disabled title="Username">
                    <?php
                }
            ?>
            <p>
                Email
                <input type="email" name="email" title="Email">
            </p>
            <p>
                Password
                <input type="password" name="password" title="Password">
            </p>
            <p>
                Confirm Password
                <input type="password" name="confirm_password" title="Confirm Password">
            </p>
            <?php
                if( isset( $content->recaptcha ) && empty( $content->recaptcha ) == false )
                {

                    echo( $content->recaptcha->html );
                }
            ?>
            <p>
                <input type="submit" value="Login" title="Submit">
            </p>
        </form>
        <p>
            <a href="<?=$url_root?>">Go home</a>
        </p>
    </body>
    <?php
        Flight::render("components/footer");
    ?>
</html>