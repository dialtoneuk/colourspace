<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 29/06/2018
 * Time: 23:40
 */

namespace Colourspace\Database\Tables;

use Colourspace\Database\Table;

class Users extends Table
{

    /**
     * @return string
     */

    public function name()
    {

        return "users";
    }

    /**
     * The map for the users table
     *
     * @return array
     */

    public function map()
    {

        return [
            'userid'    => FIELD_TYPE_INCREMENTS,
            'username'  => FIELD_TYPE_STRING,
            'password'  => FIELD_TYPE_STRING,
            'salt'      => FIELD_TYPE_STRING,
            'group'     => FIELD_TYPE_STRING,
            'colour'    => FIELD_TYPE_STRING,
            'creation'  => FIELD_TYPE_TIMESTAMP
        ];
    }

    /**
     * @param $userid
     * @return bool
     */

    public function exist( $userid )
    {

        return( $this->query()->where(['userid' => $userid])->get()->isEmpty() );
    }

    /**
     * @param $userid
     * @return \Illuminate\Support\Collection
     */

    public function get( $userid )
    {

        return( $this->query()->where(['userid' => $userid ] )->get() );
    }

    /**
     * @param $username
     * @return \Illuminate\Support\Collection
     */

    public function find( $username )
    {

        return( $this->query()->where(['username' => $username ] )->get() );
    }
}