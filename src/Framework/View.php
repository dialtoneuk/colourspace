<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 16:53
 */

namespace Colourspace\Framework;


use Colourspace\Framework\Returns\Page;
use Colourspace\Framework\Interfaces\ModelInterface;
use Colourspace\Framework\Interfaces\ViewInterface;
use Colourspace\Framework\Util\ScriptBuilder;

class View implements ViewInterface
{
    /**
     * @var ModelInterface
     */

    public $model;

    /**
     * @param ModelInterface $model
     */

    public function setModel(ModelInterface $model)
    {
        $this->model = $model;
    }

    /**
     * @return Page
     * @throws \Error
     */

    public function get()
    {

        $return = new Page();
        $return->setArray($array = [
            "file"    => "index",
            "model"     => $this->model->toArray()
        ]);
        return( $return );
    }

    /**
     * @return mixed
     */

    private function model()
    {

        return( $this->model->toArray() );
    }
}