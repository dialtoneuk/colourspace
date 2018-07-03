<?php
namespace Colourspace\Database;
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 29/06/2018
 * Time: 22:56
 */

use Colourspace\Database\Util\OpenSSL;
use Colourspace\Framework\Util\Debug;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Database\Capsule\Manager;

class Connection
{

    /**
     * @var OpenSSL
     */
    protected $openssl;
    //Location of the keys to cross reference our database settings with for verification
    private $verification = '/config/database_verification.json';
    /**
     * @var \Illuminate\Database\Capsule\Manager
     */
    private $capsule;

    /**
     * Test
     * @var \Illuminate\Database\Connection
     */
    public $connection;
    //The settings object
    public $settings;

    /**
     * Connection constructor.
     * @param bool $auto_create
     * @throws \Error
     */

    public function __construct( $auto_create = true )
    {

        Debug::message("Created connection class");

        $this->settings = $this->getSettings();
        $this->openssl = new OpenSSL();

        if ( $auto_create )
            $this->create();
    }

    /**
     * @param null $custom_settings
     * @throws \Error
     */

    public function create( $custom_settings = null )
    {

        Debug::message("Creating database connection");

        if( $custom_settings )
        {

            if( $this->verify( $custom_settings ) == false )
                throw new \Error('Custom settings are invalid');

            $settings = $custom_settings;
        }
        else
            $settings = $this->settings;

        $this->capsule = new Manager();

        if ( is_array( $settings ) == false )
            json_decode( json_encode( $settings), true );

        $this->capsule->addConnection( $settings );
        $this->connection = $this->capsule->getConnection();

        Debug::message("Connection created");
    }

    /**
     * @return bool
     * @throws \Error
     */

    public function test()
    {

        Debug::message('Testing connection');

        if( empty( $this->connection ) )
            return false;

        try
        {

            $this->connection->getDatabaseName();
        }
        catch ( \Error $error )
        {

            Debug::message('Failed: ' . $error->getMessage() );

            return false;
        }

        Debug::message('Success');

        return true;
    }

    /**
     * @return mixed
     * @throws \Error
     */

    private function getSettings()
    {

        if( DATABASE_ENCRYPTION )
        {

            if ( file_exists( COLOURSPACE_ROOT . DATABASE_CREDENTIALS ) == false )
                throw new \Error('Database credentials file missing');

            $object = json_decode( file_get_contents( COLOURSPACE_ROOT . DATABASE_CREDENTIALS  ), true );

            if( isset( $object['info'] ) == false )
                throw new \Error("No decryption keys found");

            $decrypted = $this->openssl->decrypt( $object, $object['info']['key'], $object['info']['iv'] );

            if( $this->verify( $decrypted ) == false )
                throw new \Error('Database credentials are invalid');

            return $decrypted;
        }
        else
        {

            if ( file_exists( COLOURSPACE_ROOT . DATABASE_CREDENTIALS ) == false )
                throw new \Error('Database credentials file missing');

            $object = json_decode( file_get_contents( COLOURSPACE_ROOT . DATABASE_CREDENTIALS  ), true );

            if ( empty( $object ) )
                throw new \Error('Database credentials empty');

            if( $this->verify( $object ) == false )
                throw new \Error('Database credentials are invalid');

            return $object;
        }
    }

    /**
     * @param $object
     * @return bool
     */

    private function verify( $object )
    {

        if ( file_exists( COLOURSPACE_ROOT . $this->verification ) == false )
            return false;

        $verification = json_decode( file_get_contents( COLOURSPACE_ROOT . $this->verification ),true );

        if ( count( $object ) != count( $verification ) )
            return false;

        foreach ( $verification as $key=>$value )
        {

            if( isset( $object[ $key ] ) == false )
                return false;

            switch( $value )
            {

                case "string":
                    if ( is_string( $value ) == false )
                        return false;
                    break;
                case ( "int" && "integer" ):
                    if( is_int( $value ) == false )
                        return false;
                    break;
                case "float":
                    if ( is_float( $value ) )
                        return false;
                    break;
            }
        }

        return true;
    }
}