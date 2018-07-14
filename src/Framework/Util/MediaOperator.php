<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 10/07/2018
 * Time: 21:29
 */

namespace Colourspace\Framework\Util;

use FFMpeg\FFMpeg;
use FFMpeg\Format\Audio\Mp3;
use FFMpeg\Format\Audio\Wav;

class MediaOperator
{

    /**
     * @var FFMpeg
     */

    protected $ffmpeg;

    /**
     * @var
     */

    protected $filepath;

    /**
     * MediaOperator constructor.
     * @param $filepath
     * @throws \Error
     */

    public function __construct( $filepath )
    {

        if( file_exists( COLOURSPACE_ROOT . $filepath ) == false )
            throw new \Error("File does not exist");

        if( windows_os() )
        {

            $this->ffmpeg = FFMpeg::create(array(
                'ffmpeg.binaries'  => COLOURSPACE_ROOT . FFMPEG_FOLDER . 'ffmpeg.exe',
                'ffprobe.binaries' => COLOURSPACE_ROOT . FFMPEG_FOLDER  . 'ffprobe.exe',
                'timeout'          => 3600,
                'ffmpeg.threads'   => 12
            ));
        }
        else
            $this->ffmpeg = FFMpeg::create(array(
                'ffmpeg.binaries'  => COLOURSPACE_ROOT . FFMPEG_FOLDER . 'ffmpeg',
                'ffprobe.binaries' => COLOURSPACE_ROOT . FFMPEG_FOLDER  . 'ffprobe',
                'timeout'          => 3600,
                'ffmpeg.threads'   => 12
            ));



        $this->filepath = $filepath;
    }

    /**
     * @param int $width
     * @param int $height
     * @return \FFMpeg\Media\Waveform
     */

    public function getWaveform( $width=1024, $height=248)
    {

        $audio = $this->ffmpeg->open( COLOURSPACE_ROOT . $this->filepath );
        return( $audio->waveform( $width, $height ) );
    }

    /**
     * Desctruct
     */

    public function __destruct()
    {

        unset( $this->ffmpeg );
    }

    /**
     * @param $filepath
     * @throws \Error
     */

    public function toMP3( $filepath )
    {

        if( $this->getExtension() == "mp3" )
            throw new \Error("File already a MP3");

        $audio = $this->ffmpeg->open( COLOURSPACE_ROOT . $this->filepath );
        $audio->save( new Mp3, $filepath );
    }

    /**
     * @param $filepath
     * @throws \Error
     */

    public function toWAV( $filepath )
    {

        if( $this->getExtension() == "wav" )
            throw new \Error("File already a wav");

        $audio = $this->ffmpeg->open( COLOURSPACE_ROOT . $this->filepath );
        $audio->save( new Wav, $filepath );
    }

    /**
     * @return mixed
     */

    public function getExtension()
    {

        $parts = pathinfo(COLOURSPACE_ROOT . $this->filepath );

        return( $parts["extension"] );
    }
}