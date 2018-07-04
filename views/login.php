<html>
    <?php
        Flight::render("components/head");
    ?>
    <body>
        <?php
            Flight::render("components/form_alerts");
        ?>
        <h1>
            Login
        </h1>
        <form method="POST">
            <p>
                Email
                <input type="email" name="email" title="Email">
            </p>
            <p>
                Password
                <input type="password" name="password" title="password">
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