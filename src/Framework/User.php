<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 00:25
 */
namespace Colourspace\Framework;

use Colourspace\Database\Tables\Users;
use Colourspace\Framework\Util\Colours;
use Colourspace\Framework\Util\Format;

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

    /**
     * @param $username
     * @param $email
     * @param $password
     * @param null $salt
     * @param null $group
     * @param null $colour
     * @return int
     * @throws \Error
     */

    public function register( $username, $email, $password, $salt=null, $group=null, $colour=null )
    {

        if( $salt == null )
            $salt = $this->salt();

        if( $group == null )
            $group = GROUP_DEFAULT;

        if( $colour == null )
            $colour = Colours::generate();

        $array = [
            'username'  => $username,
            'password'  => Format::saltedPassword( $salt, $password ),
            'email'     => $email,
            'salt'      => $salt,
            'group'     => $group,
            'colour'    => $colour,
            'creation'  => Format::timestamp()
        ];

        return( $this->table->insert( $array ) );
    }

    /**
     * @return string
     */

    private function salt()
    {

        return( base64_encode( openssl_random_pseudo_bytes(32)));
    }
}