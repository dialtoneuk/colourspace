<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 16:53
 */

namespace Colourspace\Framework;


use Colourspace\Framework\Interfaces\ModelInterface;
use Colourspace\Framework\Interfaces\ViewInterface;

class View implements ViewInterface
{

    /**
     * @var ModelInterface
     */

    public $model;

    public function setModel(ModelInterface $model)
    {
        $this->model = $model;
    }

    public function get()
    {

        return([
           'default.php',
           $this->model->toArray()
        ]);
    }
}