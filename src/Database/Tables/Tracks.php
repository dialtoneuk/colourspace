<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 05/07/2018
 * Time: 20:03
 */

namespace Colourspace\Database\Tables;


use Colourspace\Database\Table;
use Colourspace\Framework\Util\Format;

class Tracks extends Table
{

    /**
     * @return array
     */

    public function map()
    {

        return([
            "trackid"   => FIELD_TYPE_INCREMENTS,
            "userid"    => FIELD_TYPE_INT,
            "trackname" => FIELD_TYPE_STRING,
            "streams"    => FIELD_TYPE_STRING,
            "metainfo"  => FIELD_TYPE_JSON,
            "colour"    => FIELD_TYPE_STRING,
            "privacy"   => FIELD_TYPE_STRING,
            "creation"  => FIELD_TYPE_TIMESTAMP
        ]);
    }

    /**
     * @return string
     */

    public function name()
    {

        return "tracks";
    }

    /**
     * @param $trackid
     * @return \Illuminate\Support\Collection
     */

    public function get( $trackid )
    {

        return( $this->query()->where(["trackid" => $trackid ] )->get() );
    }

    /**
     * @param $trackname
     * @return \Illuminate\Support\Collection
     */

    public function find( $trackname )
    {

        return( $this->query()->where(['trackname' => $trackname ] )->get() );
    }

    /**
     * @param $trackid
     * @return bool
     */

    public function has( $trackid )
    {

        return( $this->query()->where(["trackid" => $trackid ] )->get()->isNotEmpty() );
    }

    /**
     * @param $userid
     * @return \Illuminate\Support\Collection
     */

    public function userTracks( $userid )
    {

        return( $this->query()->where(["userid" => $userid ] )->get() );
    }

    /**
     * @param $trackid
     * @param string $data
     * @throws \Error
     */

    public function updateMetadata( $trackid, string $data )
    {

        json_encode( $data );

        if( json_last_error() !== JSON_ERROR_NONE )
            throw new \Error("Json invalid: " . json_last_error_msg() );

        $this->query()->where( [ "trackid" => $trackid ] )->update(["metainfo" => Format::largeText( $data ) ] );
    }

    /**
     * @param $trackid
     * @param null $privacy
     * @throws \Error
     */

    public function updatePrivacy( $trackid, $privacy=null )
    {

        if( $privacy = null )
            $privacy = TRACK_PRIVACY_PUBLIC;

        if( $privacy !== ( TRACK_PRIVACY_PUBLIC || TRACK_PRIVACY_PERSONAL || TRACK_PRIVACY_PRIVATE ) )
            throw new \Error("Invalid privacy state");

        $this->query()->where([ "trackid" => $trackid ])->update(["privacy" => $privacy ]);
    }

    /**
     * @param $trackid
     */

    public function delete( $trackid )
    {

        $this->query()->where( [ "trackid" => $trackid ] )->delete();
    }
}