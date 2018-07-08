<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 08/07/2018
 * Time: 21:16
 */

namespace Colourspace\Framework\Interfaces;


interface ReturnsInterface
{

    /**
     * @param array $array
     * @return mixed
     */

    public function setArray( array $array );

    /**
     * @param array $array
     */

    public function add( array $array );

    /**
     * @return mixed
     */

    public function process();

    /**
     * @param bool $array
     * @return mixed
     */

    public function get( $array=true );
}