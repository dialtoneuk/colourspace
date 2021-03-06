<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 16:49
 */

namespace Colourspace\Framework;


use Colourspace\Framework\Interfaces\ModelInterface;
use Colourspace\Framework\Util\Debug;

class Model implements ModelInterface
{

    protected $object;

    /**
     * Model constructor.
     */

    public function __construct()
    {
        $this->object = new \stdClass();
    }

    /**
     * @throws \Error
     */

    public function startup()
    {

        if( DEBUG_ENABLED )
            Debug::message("Startup called in model: " .  __CLASS__ );
    }

    /**
     * @param $name
     * @return mixed
     */

    public function __get($name)
    {

        return( $this->object->$name );
    }

    /**
     * @param $name
     * @param $value
     * @return mixed
     */

    public function __set($name, $value)
    {

        return( $this->object->$name = $value );
    }

    /**
     * @param $name
     * @return bool
     */

    public function __isset($name)
    {

        return( isset( $this->object->$name ) );
    }

    /**
     * @return mixed
     */

    public function toArray()
    {

        return ( json_decode( json_encode( $this->object ), true ) );
    }

    /**
     * @return string
     */

    public function toJson()
    {

        return( json_encode( $this->object ) );
    }

    /**
     * @param $type
     * @param $value
     */

    public function formError($type, $value)
    {

        if ( isset( $this->object->errors ) == false )
            $this->object->errors = [];

        $this->object->errors[] = [
            "type" => $type,
            "value" => $value
        ];
    }

    /**
     * @param $type
     * @param $value
     */

    public function formMessage($type, $value)
    {

        if ( isset( $this->object->messages ) == false )
            $this->object->messages = [];

        $this->object->messages[] = [
            "type" => $type,
            "value" => $value
        ];
    }

    /**
     * @param $url
     * @param int $delay
     */

    public function redirect( $url, $delay=0 )
    {

        if( isset( $this->object->redirects ) == false )
            $this->object->redirects = [];

        $this->object->redirect[] = [
            "url" => $url,
            "delay" => $delay
        ];
    }
}