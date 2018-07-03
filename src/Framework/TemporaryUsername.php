<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 03/07/2018
 * Time: 16:25
 */

namespace Colourspace\Framework;

use Colourspace\Container;
use Colourspace\Database\Tables\TemporaryUsernames;
use Colourspace\Framework\Util\Format;

class TemporaryUsername
{

    /**
     * @var TemporaryUsernames
     */

    protected $table;

    /**
     * @var Session
     */

    protected $session;

    /**
     * TemporaryUsername constructor.
     * @throws \Error
     */

    public function __construct()
    {

        if( Container::has('application') == false )
            throw new \Error('Application has not been initialized');

        $this->table = new TemporaryUsernames();
        $this->session = Container::get('application')->session;
    }

    /**
     * @param $sessionid
     * @return bool
     */

    public function has( $sessionid )
    {

        if( $this->table->has( $sessionid ) == false )
            return false;

        return true;
    }

    /**
     * @param $sessionid
     * @return \Illuminate\Support\Collection
     */

    public function get( $sessionid )
    {

        return( $this->table->get( $sessionid )->first() );
    }

    /**
     * @param null $sessionid
     * @param null $username
     * @param $ipaddress
     * @throws \Error
     */

    public function add( $sessionid=null, $username=null, $ipaddress )
    {

        if( $sessionid == null )
        {

            if( $this->session->isActive() == false )
                throw new \Error('cannot predertermin session id as session is invalid');

            $sessionid = session_id();
        }

        if( $username == null )
            $username = $this->generate();

        if( $this->has( $sessionid ) )
            return;

        $this->table->insert([
            "sessionid" => $sessionid,
            "username"  => $username,
            "ipaddress" => $ipaddress,
            "creation"  => Format::Timestamp()
        ]);
    }

    /**
     * @return string
     */

    public function generate()
    {

        $username = ACCOUNT_PREFIX;
        $digits = "";

        for( $i = 0; $i < ACCOUNT_DIGITS; $i++ )
        {

            $digits = $digits . rand( ACCOUNT_RND_MIN, ACCOUNT_RND_MAX );
        }

        return( $username . $digits );
    }
}