<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 07/07/2018
 * Time: 12:13
 */

namespace Colourspace\Framework;

use BoyHagemann\Wave\Wave;
use BoyHagemann\Waveform\Generator\Png;
use BoyHagemann\Waveform\Waveform as WaveFormClass;
use Colourspace\Framework\Util\FileOperator;
use BoyHagemann\Waveform\Generator;

class WaveForm
{

    /**
     * @var WaveFormClass
     */
    protected $waveform;

    /**
     * @var string
     */

    protected $filepath;

    /**
     * WaveForm constructor.
     * @param $filepath
     * @param bool $auto_create
     * @throws \Error
     */

    public function __construct( $filepath, $auto_create=true )
    {

        if( file_exists( COLOURSPACE_ROOT . $filepath ) == false )
            throw new \Error("File does not exist");

        if( FileOperator::checkExtension( $filepath, ["mp3","wav","flac"] ) == false )
            throw new \Error("Invalid type");

        $this->filepath = $filepath;

        if( $auto_create )
            $this->create();
    }

    public function create()
    {

        $this->waveform = WaveFormClass::fromFilename( $this->filepath );
    }

    /**
     * @return mixed
     */

    public function svg()
    {

        $waveform = $this->waveform->setGenerator( new Generator\Svg );

        return( $waveform->generate() );
    }

    /**
     * @param int $height
     * @param int $width
     * @return mixed
     */

    public function generate( $height=218, $width=1024 )
    {

        $generator = new Generator\Html();

        $waveform = $this->waveform->setGenerator( $generator )
            ->setHeight( $height )
            ->setWidth( $width );

        return( $waveform->generate() );
    }
}