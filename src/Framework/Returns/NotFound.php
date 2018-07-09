<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 08/07/2018
 * Time: 21:17
 */

namespace Colourspace\Framework\Returns;


use Colourspace\Framework\Interfaces\ReturnsInterface;
use Colourspace\Framework\Util\Debug;
use Flight;

class NotFound implements ReturnsInterface
{

    /**
     * @var array
     */

    protected $array;

    /**
     * @param array $array
     * @return mixed|void
     */

    public function setArray( array $array )
    {

        $this->array = $array;
    }

    /**
     * @return mixed|void
     * @throws \Error
     */

    public function process()
    {

        if( DEBUG_ENABLED )
            Debug::message( print_r( $this->array ) );

        Flight::notFound();
    }

    /**
     * @param array $array
     */

    public function add(array $array)
    {

        $this->array = array_merge( $this->array, $array );
    }

    /**
     * @param bool $array
     * @return mixed
     */

    public function get( $array=true )
    {

        if( $array )
            return( $this->array );

        return( json_encode( $this->array ) );
    }
}