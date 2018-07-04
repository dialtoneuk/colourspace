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
            <input type="email" name="email" title="Email">
            <input type="text" name="password" title="password">
            <input type="submit" value="Login" title="Submit">
        </form>

    </body>
    <?php
        Flight::render("components/footer");
    ?>
</html>