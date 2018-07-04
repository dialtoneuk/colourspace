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
            <input type="text" value="user00000001" name="username" disabled title="Username">
            <input type="email" name="email" title="Email">
            <input type="text" name="password" title="Password">
            <input type="text" name="confirm_password" title="Confirm Password">
            <input type="submit" value="Login" title="Submit">
        </form>
        <p>
            <a href="<?=$url_root?>">Go home</a>
        </p>
    </body>
    <?php
        Flight::render("components/footer");
    ?>
</html>