<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 14:55
 */
namespace Colourspace\Framework\Interfaces;

interface ControllerInterface
{

    public function setModel( ModelInterface $model );

    /**
     * @return array
     */

    public function keyRequirements();

    public function before();

    public function process( string $type, $data );

    public function authentication( string $type, $data );
}