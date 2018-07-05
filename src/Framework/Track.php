<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 05/07/2018
 * Time: 20:16
 */

namespace Colourspace\Framework;


use Colourspace\Database\Tables\Tracks;
use Colourspace\Framework\Util\Colours;
use Colourspace\Framework\Util\Format;

class Track
{

    protected $table;

    /**
     * Track constructor.
     * @throws \Error
     */

    public function __construct()
    {

        $this->table = new Tracks();
    }

    /**
     * @param $trackid
     * @return bool
     */

    public function exists( $trackid )
    {

        return( $this->table->has( $trackid ) );
    }

    /**
     * @param $trackid
     * @return mixed
     */

    public function get( $trackid )
    {

        return( $this->table->get( $trackid ) )->first();
    }

    /**
     * @param $trackid
     * @param bool $object
     * @return mixed
     * @throws \Error
     */

    public function metadata( $trackid, $object=true )
    {

        $track = $this->get( $trackid );

        if( isset( $track->metadata ) == false )
            throw new \Error("Invalid key");

        $json = Format::decodeLargeText( $track->metadata );

        if( empty( $json ) )
            throw new \Error("Invalid metadata");

        $json = json_decode( $json, $object );

        if( json_last_error() !== JSON_ERROR_NONE )
            throw new \Error("Json Error:" . json_last_error_msg() );

        return( $json );
    }

    /**
     * @param $knownas
     * @param $description
     * @param array $credits
     * @param string $waveform
     * @return array
     */

    public function getMetadataArray( $knownas, $description, $credits, $waveform )
    {


        return([
            "aka"           => $knownas,
            "description"   => $description,
            "credits"       => $credits,
            "waveform"      => $waveform
        ]);
    }

    /**
     * @param $trackid
     * @param array $values
     * @throws \Error
     */

    public function updateMetadata( $trackid, array $values )
    {

        $metadata = $this->metadata( $trackid, false );

        if( empty( $metadata ) )
            throw new \Error("Invalid metadata");

        foreach( $values as $key=>$value )
            $metadata[ $key ] = $value;

        $this->table->updateMetadata( $trackid, json_encode( $metadata ) );
    }

    /**
     * @param $trackid
     * @param bool $array
     * @return mixed
     */

    public function streams( $trackid, $array=true )
    {

        return( json_decode( Format::decodeLargeText( $this->get( $trackid )->streams ), $array ) );
    }

    /**
     * @param $trackid
     * @param $streamtype
     * @param $streamurl
     * @throws \Error
     */

    public function addStream( $trackid, $streamtype, $streamurl )
    {

        $streams = $this->streams( $trackid );

        if( isset( $streams[ $streamtype ] ) )
            throw new \Error("Stream type already set");

        $streams[ $streamtype ] = $streamurl;
        $this->updateStreams( $trackid, $streams );
    }

    /**
     * @param $trackid
     * @param $streamtype
     * @throws \Error
     */

    public function removeStream( $trackid, $streamtype )
    {

        $streams = $this->streams( $trackid );

        if( isset( $streams[ $streamtype ] ) == false  )
            throw new \Error("Stream type missing");

        unset( $streams[ $streamtype ] );
        $this->updateStreams( $trackid, $streams );
    }

    /**
     * @param $trackid
     * @param $streams
     */

    public function updateStreams( $trackid, $streams )
    {

        $this->table->update(["trackid" => $trackid ], ["streams" => Format::largeText( $streams ) ] );
    }

    /**
     * @param int $userid
     * @param array $streams
     * @param string|null $trackname
     * @param $metainfo
     * @param null $colour
     * @param string|null $privacy
     * @return int
     * @throws \Error
     */

    public function create( int $userid, array $streams, string $trackname=null, $metainfo, $colour=null, string $privacy=null )
    {

        if( $trackname == null )
            $trackname = $this->generate();

        if( $colour == null )
            $colour = Colours::generate();

        if( $privacy == null )
            $privacy = TRACK_PRIVACY_PUBLIC;

        return( $this->table->insert([
            "userid" => $userid,
            "trackname" => $trackname,
            "streams"   => Format::largeText( json_encode( $streams ) ),
            "metainfo"  => Format::largeText( json_encode( $metainfo ) ),
            "colour"    => $colour,
            "privacy"   => $privacy,
            "creation"  => Format::timestamp()
        ], false ) );
    }

    /**
     * @return string
     */

    private function generate()
    {

        $username = TRACK_PREFIX;
        $digits = "";

        for( $i = 0; $i < TRACK_DIGITS; $i++ )
        {

            $digits = $digits . rand( TRACK_RND_MIN, TRACK_RND_MAX );
        }

        return( $username . $digits );
    }
}