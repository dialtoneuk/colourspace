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
            'userid' => LARAVEL_TYPE_INCREMENTS,
            'username' => LARAVEL_TYPE_STRING,
            'password' => LARAVEL_TYPE_STRING,
            'salt' => LARAVEL_TYPE_STRING,
            'groupid' => LARAVEL_TYPE_INT,
            'creation' => LARAVEL_TYPE_TIMESTAMP
        ];
    }

    /**
     * @param $userid
     * @return bool
     */

    public function exist( $userid )
    {

        return( $this->query()->where(['userid' => $userid])->exists() );
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

        return( $this->query()->where(['username', $username ] )->get() );
    }

    /**
     * @param $values
     * @param bool $verify
     * @return int
     * @throws \Error
     */

    public function insert( $values, $verify=true )
    {

        if ( $verify )
        {
            if ( $this->verify( $values ) == false )
                throw new \Error('Values are incorrect for this table');
        }

        return( $this->query()->insertGetId( $values ) );
    }
}