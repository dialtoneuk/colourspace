<?php
namespace Colourspace;
use Colourspace\Database\Connection;
use Colourspace\Framework\FrontController;
use Colourspace\Framework\Session;
use Colourspace\Framework\Router;

/**
 * @property Session session
 * @property FrontController frontcontroller
 * @property Connection connection
 * @property Router router
 */
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