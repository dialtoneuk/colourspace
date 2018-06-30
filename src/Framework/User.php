<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 00:25
 */
namespace Colourspace\Framework;

use Colourspace\Database\Tables\Users;

class User
{

    /**
     * @var Users
     */

    protected $table;

    /**
     * User constructor.
     * @throws \Error
     */

    public function __construct()
    {

        $this->table = new Users();
    }

    /**
     * @param $username
     * @return bool
     */

    public function exists( $username )
    {

        if( $this->table->find( $username )->isEmpty() )
            return false;

        return true;
    }

    public function hasEmail( $email )
    {

        if( $this->table->search('email', $email )->isEmpty() )
            return false;

        return true;
    }

    /**
     * @param $email
     * @return bool
     */

    public function emailUnique( $email )
    {

        return( !$this->hasEmail( $email ) );
    }
    /**
     * @param $userid
     * @return bool
     */

    public function has( $userid )
    {

        return( $this->table->exist( $userid ) );
    }

    /**
     * @param $userid
     * @return mixed
     */

    public function get( $userid )
    {

        return( $this->table->get( $userid )->first() );
    }

    /**
     * @param $email
     * @return mixed
     */

    public function getByEmail( $email )
    {

        return( $this->table->search('email', $email )->first() );
    }

    /**
     * @param $userid
     * @return mixed
     */

    public function group( $userid )
    {

        return( $this->get( $userid )->group );
    }

    public function register( $username, $email, $password, $group=null )
    {


    }
}