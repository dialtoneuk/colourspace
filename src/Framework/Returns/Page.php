<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 08/07/2018
 * Time: 21:17
 */

namespace Colourspace\Framework\Returns;


use Colourspace\Framework\Interfaces\ReturnsInterface;
use Colourspace\Framework\Util\ScriptBuilder;
use Colourspace\Framework\Util\Debug;
use Flight;


class Page implements ReturnsInterface
{

    /**
     * @var ScriptBuilder
     */

    protected $script_builder;

    /**
     * @var array
     */

    protected $array;

    /**
     * Page constructor.
     * @throws \Error
     */

    public function __construct()
    {

        $this->script_builder = new ScriptBuilder();
    }

    /**
     * @param array $array
     * @return mixed|void
     * @throws \Error
     */

    public function setArray( array $array )
    {

        if( is_array( $array ) == false )
            throw new \Error("Must be array");

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

        $this->array = $array;
    }

    /**
     * @return mixed|void
     * @throws \Error
     */

    public function process()
    {

        $array = $this->get();

        if ( empty( $array ) == false && is_array( $array ) )
        {

            if( isset( $array['file'] ) == false )
                throw new \Error('No file');

            if( isset( $array['model'] ) == false )
                throw new \Error('No model');

            if( isset( $array['footer'] ) == false )
                $array["footer"] = [];

            if( isset( $array['header'] ) == false )
                $array["footer"] = [];

            $object = array_merge( $array['model'], [
                "footer" => $array['footer'],
                "header" => $array['header'],
            ]);

            if( FLIGHT_MODEL_OBJECT )
                $object = json_decode( json_encode( $object ) );

            Flight::view()->set( FLIGHT_MODEL_DEFINITION , $object );

            if( FLIGHT_SET_GLOBALS )
            {

                Flight::view()->set("url_root", COLOURSPACE_URL_ROOT );
                Flight::view()->set("document_root", COLOURSPACE_ROOT );

                if( DEBUG_ENABLED )
                    Flight::view()->set("debug_messages", Debug::getMessages() );
            }

            Flight::render( $array['file'] );
        }
        else
            Flight::redirect( COLOURSPACE_URL_ROOT );
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

        return( json_decode( json_encode( $this->array ), $array ) );
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
}