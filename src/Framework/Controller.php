<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 14:58
 */

namespace Colourspace\Framework;


use Colourspace\Container;
use Colourspace\Framework\Interfaces\ControllerInterface;
use Colourspace\Framework\Interfaces\ModelInterface;
use Colourspace\Framework\Util\Debug;

class Controller implements ControllerInterface
{

    /**
     * @var ModelInterface
     */

    public $model;

    /**
     * @param ModelInterface $model
     * @throws \Error
     */

    public function setModel( ModelInterface $model )
    {

        if( empty( $model ) )
            throw new \Error('Model is invalid');

        $this->model = $model;
    }

    /**
     * @return array
     */

    public function keyRequirements()
    {

        return [];
    }

    /**
     * @throws \Error
     */

    public function before()
    {

        if( DEBUG_ENABLED )
            Debug::message("Controller initiating process method");
    }

    /**
     * @param string $type
     * @param $data
     */

    public function process(string $type, $data)
    {

        switch ( $type )
        {

            case MVC_REQUEST_POST:
                print_r( $data );
                break;
            case MVC_REQUEST_GET:
                print_r( $data );
                break;
            case MVC_REQUEST_PUT:
                print_r( $data );
                break;
            case MVC_REQUEST_DELETE:
                print_r( $data );
                break;
        }
    }

    /**
     * @param string $type
     * @param $data
     * @return bool
     * @throws \Error
     */

    public function authentication(string $type, $data)
    {

        if ( Container::has('application') == false )
            throw new \Error('Application has not been initialized');

        $application = Container::get('application');

        if( $application->session->isLoggedIn() )
            return false;

        return true;
    }

    /**
     * @param $data
     * @return bool
     */

    public function check( $data )
    {

        if( empty( $data ) || is_array( $data ) == false )
            return false;

        if( empty( $this->keyRequirements() ) )
            return true;

        foreach ( $this->keyRequirements() as $requirement )
        {

            if( isset( $data[ $requirement ] ) == false )
                return false;
        }

        return true;
    }

    /**
     * @param $data
     * @param bool $object
     * @return array|null|\stdClass
     */

    public function pickKeys( $data, $object=true )
    {

        if( empty( $this->keyRequirements() ) )
            return null;

        if( $object )
            $result = [];
        else
            $result = new \stdClass();

        foreach( $this->keyRequirements() as $requirement )
        {

            if( $object )
                $result->$requirement = $data[ $requirement ];
            else
                $result[ $requirement ] = $data[ $requirement ];
        }

        return $result;
    }
}