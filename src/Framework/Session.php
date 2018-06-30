<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 00:25
 */
namespace Colourspace\Framework;

use Colourspace\Database\Tables\Sessions;

class Session
{

    /**
     * @var Sessions
     */

    protected $table;

    /**
     * Session constructor.
     * @param bool $auto_initialize
     * @throws \Error
     */

    public function __construct( $auto_initialize= true )
    {
        if( $auto_initialize )
            $this->initialize();
    }

    /**
     * @throws \Error
     */

    public function initialize()
    {

        $this->table = new Sessions();

        if( session_status() == PHP_SESSION_ACTIVE )
            throw new \Error('Session has already been initiated');

        session_start();
    }

    /**
     * @return bool
     */

    public function isActive()
    {

        if( session_status() == PHP_SESSION_ACTIVE )
            return true;

        return false;
    }

    /**
     * @param bool $clear_session_data
     * @throws \Error
     */

    public function destroy( $clear_session_data=true )
    {

        if( $this->isLoggedIn() == false )
            throw new \Error('Cannot destroy if user is not logged in');

        $this->table->remove( session_id() );

        if( $clear_session_data )
            $_SESSION = [];

        session_regenerate_id( true );
    }

    /**
     * @return int
     * @throws \Error
     */

    public function userid()
    {

        if( $this->isLoggedIn() == false )
            throw new \Error('Cannot obtain userid if user is not logged in');

        return( $this->table->get( session_id() )->first()->userid );
    }

    /**
     * @param $userid
     * @return bool
     * @throws \Error
     */

    public function Login( $userid )
    {

        if( $this->isLoggedIn() )
            return false;

        $this->table->insert([
            'userid' => $userid,
            'sessionid' => session_id(),
            'ipaddress' => $_SERVER['REMOTE_ADDR'],
            'creation' => microtime()
        ]);

        return true;
    }

    /**
     * @return bool
     * @throws \Error
     */

    public function isLoggedIn()
    {

        if( empty( session_id( ) ) )
            throw new \Error('No session id, check if session has been initiated');

        if( $this->table->exist( session_id() ) )
            return false;

        return true;
    }

    /**
     * @return bool
     * @throws \Error
     */

    public function verifyAddress()
    {

        //If we aren't even logged in
        if( $this->isLoggedIn() == false )
            return false;

        //If we are local host
        if( $_SERVER['REMOTE_ADDR'] == "::1" || $_SERVER['REMOTE_ADDR'] == null )
            return true;

        if( $this->table->get( session_id() )->first()->ipaddress !== $_SERVER['REMOTE_ADDR'] )
            return false;

        return true;
    }
}