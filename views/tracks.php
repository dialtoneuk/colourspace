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
                            <div style="width: 200px; height:200px; background-color: #<?=$track->colour?>"></div>
                            <h1><?=$track->trackname?></h1>
                        <?php

                            $streams = json_decode( Format::decodeLargeText( $track->streams ), true );

                            $metainfo = json_decode( Format::decodeLargeText( $track->metainfo ) );

                            if( $metainfo->waveform !== null )
                            {
                                ?>
                                <img src="<?=$metainfo->waveform?>" height="248" width="812" alt="Waveform"><br>
                                <?php
                            }

                        foreach( $streams as $type=>$stream )
                        {
                            ?>
                            <audio controls>
                                <source src="<?=AMAZON_BUCKET_URL . $stream?>" type="audio/<?=$type?>">
                            </audio>

                            <?php
                        }

                        if( $metainfo->description !== null )
                            {

                                $markdown = new \Colourspace\Framework\Util\Markdown();
                                echo( $markdown->markdown( $metainfo->description ) );
                            }
                        ?>

                        <?php
                    }
                }
            ?>
        </section>

        <p>
            <a href="upload">Upload a track</a>
        </p>

    <?php
        Flight::render("components/footer");
    ?>
</html>
