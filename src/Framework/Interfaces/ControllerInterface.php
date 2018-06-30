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

    public function process( string $type, $data );

    public function authentication( string $type, $data );
}