<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 21:44
 */

namespace Colourspace\Database\Tables;


use Colourspace\Database\Table;

class TemporaryUsernames extends Table
{

    /**
     * @return array
     */

    public function map()
    {

        return( [
            "sessionid" => FIELD_TYPE_STRING,
            "username"  => FIELD_TYPE_STRING,
            "ipaddress" => FIELD_TYPE_IPADDRESS,
            "creation"  => FIELD_TYPE_TIMESTAMP
        ]);
    }

    /**
     * @return string
     */

    public function name()
    {

        return "temporary_usernames";
    }

    /**
     * @param $sessionid
     * @return \Illuminate\Support\Collection
     */

    public function get( $sessionid )
    {

        return( $this->query()->where(['sessionid' => $sessionid ] )->get() );
    }

    /**
     * @param $sessionid
     * @return bool
     */

    public function has( $sessionid )
    {

        return( !$this->query()->where( ['sessionid' => $sessionid ] )->get()->isEmpty() );
    }

    /**
     * @param $username
     * @return \Illuminate\Support\Collection
     */

    public function find( $username )
    {

        return( $this->query()->where(['username'  => $username ])->get() );
    }
}