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
use Colourspace\Framework\Util\ScriptBuilder;

class View implements ViewInterface
{

    protected $script_builder;

    /**
     * @var ModelInterface
     */

    public $model;

    /**
     * View constructor.
     * @throws \Error
     */

    public function __construct()
    {

        if( SCRIPT_BUILDER_ENABLED )
            $this->script_builder = new ScriptBuilder();
    }

    /**
     * @param ModelInterface $model
     */

    public function setModel(ModelInterface $model)
    {
        $this->model = $model;
    }

    /**
     * @return array
     * @throws \Error
     */

    public function get()
    {

        $array = [
            "render"    => "index",
            "model"     => $this->model->toArray()
        ];

        if( SCRIPT_BUILDER_ENABLED )
        {
            $this->buildScripts();

            $array["footer"] = [
                SCRIPT_BUILDER_COMPILED
            ];
        }
        else
            $array["footer"] = [];

        $array["header"] = [
                "/assets/js/" . FLIGHT_JQUERY_FILE
        ];

        return( $array );
    }

    /**
     * @throws \Error
     */

    private function buildScripts()
    {

        if( SCRIPT_BUILDER_ENABLED == false )
            return;

        $this->script_builder->build();
    }

    /**
     * @return mixed
     */

    private function model()
    {

        return( $this->model->toArray() );
    }
}