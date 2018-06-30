<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 29/06/2018
 * Time: 23:40
 */

namespace Colourspace\Database\Tables;

use Colourspace\Database\Table;

class Sessions extends Table
{

    /**
     * @return string
     */

    public function name()
    {

        return "sessions";
    }

    /**
     * The map for the users table
     *
     * @return array
     */

    public function map()
    {

        return [
            'userid' => LARAVEL_TYPE_INCREMENTS,
            'sessionid' => LARAVEL_TYPE_STRING,
            'ipaddress' => LARAVEL_TYPE_STRING,
            'creation' => LARAVEL_TYPE_TIMESTAMP
        ];
    }

    /**
     * @param $sessionid
     * @return bool
     */

    public function exist( $sessionid )
    {

        return( $this->query()->where(['sessionid' => $sessionid] )->exists() );
    }

    /**
     * @param $sessionid
     * @return \Illuminate\Support\Collection
     */

    public function get( $sessionid )
    {

        return( $this->query()->where(['sessionid' => $sessionid] )->get() );
    }

    /**
     * @param $userid
     * @return \Illuminate\Support\Collection
     */

    public function find( $userid )
    {

        return( $this->query()->where(['userid', $userid ] )->get() );
    }

    /**
     * @param $sessionid
     */

    public function remove( $sessionid )
    {

        $this->query()->where(["sessionid", $sessionid ] )->delete();
    }

    /**
     * @param $userid
     */

    public function clear( $userid )
    {

        $this->query()->where(["userid", $userid ] )->delete();
    }
}