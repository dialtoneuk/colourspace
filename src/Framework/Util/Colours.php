<?php
namespace Colourspace\Framework\Util;


class Colours
{

    /**
     * @param int $output
     * @return string
     * @throws \Error
     */

    public static function generate( $output=COLOURS_OUTPUT_HEX )
    {

        switch( $output )
        {

            case COLOURS_OUTPUT_HEX:
                return dechex(rand(0x000000, 0xFFFFFF));
                break;
            case COLOURS_OUTPUT_RGB:
                return ( rand(0,255) . "," . rand(0,255) . "," . rand(0,255) );
                break;
            default:
                throw new \Error("Unknown output");
                break;
        }
    }
}