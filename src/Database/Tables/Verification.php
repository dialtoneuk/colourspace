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
            "verificationid"    => LARAVEL_TYPE_INCREMENTS,
            "userid"            => LARAVEL_TYPE_INT,
            "type"              => LARAVEL_TYPE_STRING,
            "token"             => LARAVEL_TYPE_STRING,
            "creation"          => LARAVEL_TYPE_TIMESTAMP
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

        return( $this->query()->where(['verificationid', $verificationid ] )->get() );
    }

    /**
     * @param $verificationid
     * @return bool
     */

    public function has( $verificationid )
    {

        return( $this->query()->where(['verificationid', $verificationid ] )->exists() );
    }

    /**
     * @param $token
     * @return \Illuminate\Support\Collection
     */

    public function find( $token )
    {

        return( $this->query()->where(['token', $token])->get() );
    }

    /**
     * @param $token
     */

    public function remove( $token )
    {

        $this->query()->where(['token', $token ])->delete();
    }

    /**
     * @param $userid
     */

    public function clear( $userid )
    {

        $this->query()->where(["userid", $userid ] )->delete();
    }
}