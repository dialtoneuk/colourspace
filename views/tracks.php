<?php
use Colourspace\Framework\Util\Format;
?>
<html>
    <?php
        Flight::render("components/head");
    ?>

    <body>
        <?php
            Flight::render("components/form_alerts");
        ?>
        <h1>
            Your Tracks
        </h1>

        <section>
            <?php

                if( empty( $content->profiles->tracks ) == false )
                {

                    foreach( $content->profiles->tracks as $track )
                    {

                        ?>

                            <h1 style="color: #<?=$track->colour?>"><?=$track->trackname?></h1>
                        <?php

                            $streams = json_decode( Format::decodeLargeText( $track->streams ), true );

                            foreach( $streams as $type=>$stream )
                            {
                                ?>
                                    <audio controls>
                                        <source src="<?=AMAZON_BUCKET_URL . $stream?>" type="audio/<?=$type?>">
                                    </audio>
                                <?php
                            }


                        ?>
                            <p>
                                <?=Format::decodeLargeText( $track->metainfo )?>
                            </p>
                        <?php
                    }
                }
            ?>
        </section>

    <?php
        Flight::render("components/footer");
    ?>
</html>
