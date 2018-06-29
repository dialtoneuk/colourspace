<?php
namespace Colourspace;


class Application
{

    /**
     * @var \stdClass
     */
    private $objects;

    /**
     * Application constructor.
     * @throws \Error
     */

    public function __construct()
    {

        $this->objects = new \stdClass();
    }

    /**
     * @param $name
     * @return mixed
     */

    public function __get($name)
    {

        return $this->objects->$name;
    }

    /**
     * @param $name
     * @return bool
     */

    public function __isset($name)
    {

        return isset( $this->objects->$name );
    }

    /**
     * @param $name
     * @param $value
     */

    public function __set($name, $value)
    {

        $this->objects->$name = $value;
    }
}