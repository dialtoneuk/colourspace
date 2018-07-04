
<html>
    <?php
        Flight::render("components/head");
    ?>

    <body>
        <h1>
            COLOURSPACE
        </h1>

        <?php

            if( $content->profiles->session->loggedin )
            {

                ?>
                    <h2 style="color: <?=$content->profiles->user->colour?>">
                        HELLO <?=$content->profiles->user->username?> with the userid of <?=$content->profiles->user->userid?>
                    </h2>
                <?php
            }
        ?>
        <p>
            <marquee>
                PREMIUM WEB DESIGN
            </marquee>
        </p>
        <p>
            What would you like to test?
        </p>
        <p>
            <a href="login">Logins please!</a>
        </p>
        <p>
            <a href="logout">Let me leave this place</a>
        </p>
        <p>
            <a href="register">Hmm.. How about registering an account?</a>
        </p>
        <p>
            <a href="tracks">Screw that, just show me some tracks</a>
        </p>
        <p>
            <a href="space">Or how about some random users colourspace?</a>
        </p>
        <h2>
            SUPER SECRET DEBUG AREA
        </h2>
        <p>
            <a href="debug">Debug control center.. for all your debugging needs...</a>
        </p>
        <p>
            <a href="admin">Admin Abuse Center</a>
        </p>
        <p>
            <a href="settings">Fuck with some settings and change pointless things</a>
        </p>

        <?php


        ?>
    </body>

    <?php
        Flight::render("components/footer");
    ?>
</html>
