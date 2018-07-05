<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 21:44
 */

namespace Colourspace\Database\Tables;


use Colourspace\Database\Table;

class Verification extends Table
{

    /**
     * @return array
     */

    public function map()
    {

        return( [
            "verificationid"    => FIELD_TYPE_INCREMENTS,
            "userid"            => FIELD_TYPE_INT,
            "type"              => FIELD_TYPE_STRING,
            "token"             => FIELD_TYPE_STRING,
            "creation"          => FIELD_TYPE_TIMESTAMP
        ]);
    }

    /**
     * @return string
     */

    public function name()
    {

        return "verification";
    }

    /**
     * @param $verificationid
     * @return \Illuminate\Support\Collection
     */

    public function get( $verificationid )
    {

        return( $this->query()->where(['verificationid' => $verificationid ] )->get() );
    }

    /**
     * @param $verificationid
     * @return bool
     */

    public function has( $verificationid )
    {

        return( $this->query()->where(['verificationid' => $verificationid ] )->get()->isNotEmpty() );
    }

    /**
     * @param $token
     * @return \Illuminate\Support\Collection
     */

    public function find( $token )
    {

        return( $this->query()->where(['token' => $token])->get() );
    }

    /**
     * @param $token
     */

    public function remove( $token )
    {

        $this->query()->where(['token' => $token ])->delete();
    }

    /**
     * @param $userid
     */

    public function clear( $userid )
    {

        $this->query()->where(["userid" => $userid ] )->delete();
    }
}