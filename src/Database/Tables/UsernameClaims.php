<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 21:44
 */

namespace Colourspace\Database\Tables;


use Colourspace\Database\Table;

class UsernameClaims extends Table
{

    /**
     * @return array
     */

    public function map()
    {

        return( [
            "sessionid" => LARAVEL_TYPE_STRING,
            "username"  => LARAVEL_TYPE_STRING,
            "creation"  => LARAVEL_TYPE_TIMESTAMP
        ]);
    }

    /**
     * @return string
     */

    public function name()
    {

        return "username_claims";
    }

    /**
     * @param $sessionid
     * @return \Illuminate\Support\Collection
     */

    public function get( $sessionid )
    {

        return( $this->query()->where(['sessionid', $sessionid ] )->get() );
    }

    /**
     * @param $sessionid
     * @return bool
     */

    public function has( $sessionid )
    {

        return( $this->query()->where(['sessionid', $sessionid ] )->exists() );
    }

    /**
     * @param $username
     * @return \Illuminate\Support\Collection
     */

    public function find( $username )
    {

        return( $this->query()->where(['username', $username ])->get() );
    }
}