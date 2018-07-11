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
use Colourspace\Framework\Util\Collector;
use Colourspace\Framework\Util\Debug;

class Controller implements ControllerInterface
{

    /**
     * @var ModelInterface
     */

    public $model;

    /**
     * @var Recaptcha
     */

    protected $recaptcha;

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

        if( GOOGLE_ENABLED )
            $this->recaptcha = Collector::new("Recaptcha");

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

        Debug::message("Authentication called in base class: " . __CLASS__ );

        if ( Container::has('application') == false )
            throw new \Error('Application has not been initialized');

        $application = Container::get('application');

        if( $application->session->isLoggedIn() )
            return false;

        return true;
    }

    /**
     * @param $data
     * @param bool $empty_check
     * @return bool
     */

    public function check( $data, $empty_check=true )
    {

        if( is_object( $data ) )
            $data = json_decode( json_encode( $data ), true );

        if( empty( $data ) || is_array( $data ) == false )
            return false;

        if( empty( $this->keyRequirements() ) )
            return true;

        foreach ( $this->keyRequirements() as $requirement )
        {

            if( isset( $data[ $requirement ] ) == false )
                return false;

            if( empty( $data[ $requirement ] ) && $empty_check )
                return false;
        }

        return true;
    }

    /**
     * @param $form
     * @return bool
     * @throws \Exception
     */

    public function checkRecaptcha( $form )
    {

        if( GOOGLE_ENABLED )
        {

            return ( $this->recaptcha->isValid( $form->recaptcha ) );
        }
        else
            return true;
    }

    public function addRecaptcha()
    {

        if (GOOGLE_ENABLED)
            $this->model->recaptcha = [
                'script' => $this->recaptcha->script(),
                'html' => $this->recaptcha->html()
            ];
    }

    /**
     * @param $data
     * @param bool $object
     * @param bool $escape
     * @return array|null|\stdClass
     */

    public function pickKeys( $data, $object=true, $escape=true )
    {

        if( empty( $this->keyRequirements() ) )
            return null;

        if( is_object( $data ) )
            $data = json_decode( json_encode( $data ), true );

        if( $object == false )
            $result = [];
        else
            $result = new \stdClass();

        foreach( $this->keyRequirements() as $requirement ) {

            if( $requirement == "g-recaptcha-response")
                $header = "recaptcha";
            else
                $header = $requirement;

            if( $escape )
                $data[$requirement] = htmlspecialchars( $data[ $requirement ] );

            if( $object )
                $result->$header = $data[ $requirement ];
            else
                $result[ $header ] = $data[ $requirement ];
        }

        return $result;
    }
}