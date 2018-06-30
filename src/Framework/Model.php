<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 16:49
 */

namespace Colourspace\Framework;


use Colourspace\Framework\Interfaces\ModelInterface;

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
     * @param $name
     * @param $value
     */

    public function formError($name, $value)
    {

        if ( isset( $this->object->errors ) == false )
            $this->object->errors = [];

        $this->object->errors[] = [
            $name => $value
        ];
    }

    /**
     * @param $name
     * @param $value
     */

    public function formMessage($name, $value)
    {

        if ( isset( $this->object->messages ) == false )
            $this->object->messages = [];

        $this->object->messages[] = [
            $name => $value
        ];
    }
}