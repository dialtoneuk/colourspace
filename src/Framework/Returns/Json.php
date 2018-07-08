<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 08/07/2018
 * Time: 21:17
 */

namespace Colourspace\Framework\Returns;


use Colourspace\Framework\Interfaces\ReturnsInterface;
use Flight;

class Json implements ReturnsInterface
{

    /**
     * @var array
     */

    protected $return;

    /**
     * @param array $array
     * @return mixed|void
     */

    public function setArray( array $array )
    {

        $this->return = $array;
    }

    /**
     * @return mixed|void
     */

    public function process()
    {

        Flight::json( $this->return );
    }

    /**
     * @param array $array
     */

    public function add(array $array)
    {

        $this->return = array_merge( $this->return, $array );
    }

    /**
     * @param bool $array
     * @return mixed
     */

    public function get( $array=true )
    {

        if( $array )
            return( $this->return );

        return( json_encode( $this->return ) );
    }
}