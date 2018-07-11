<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 05/07/2018
 * Time: 20:41
 */

namespace Colourspace\Framework\Controllers;


use Colourspace\Framework\Amazon;
use Colourspace\Framework\Group;
use Colourspace\Framework\UploadManager;
use Colourspace\Framework\Util\Collector;
use Colourspace\Framework\User;
use Colourspace\Framework\Session;
use Colourspace\Framework\Track;
use Colourspace\Container;
use Colourspace\Framework\Util\MediaOperator;
use Colourspace\Framework\Util\Format;
use Colourspace\Framework\WaveForm;
use Delight\FileUpload\Throwable\Error;
use Colourspace\Framework\Util\Markdown;
use Colourspace\Framework\Util\Mp3Parser;

class Upload extends DefaultController
{

    /**
     * @var UploadManager
     */

    protected $uploads;

    /**
     * @var Amazon
     */

    protected $amazon;

    /**
     * @var Group
     */

    protected $group;

    /**
     * @var User
     */

    protected $user;

    /**
     * @var Track
     */

    protected $track;

    /**
     * @var Session
     */

    protected $session;

    /**
     * @var Markdown
     */

    protected $markdown;

    /**
     * @throws \Error
     */

    public function keyRequirements()
    {

        $array = [
            "name",
            "description",
            "privacy"
        ];

        if( GOOGLE_ENABLED )
            $array[] = "g-recaptcha-response";

        return( $array );
    }

    /**
     * @throws \Error
     */

    public function before()
    {

        parent::before();

        $this->uploads = Collector::new("UploadManager");
        $this->amazon = Collector::new("Amazon");
        $this->group = Collector::new("Group");
        $this->user = Collector::new("User");
        $this->track = Collector::new("Track");
        $this->markdown = Collector::new("Markdown", "Colourspace\\Framework\\Util\\");
        $this->session = Container::get('application')->session;
    }

    /**
     * @param string $type
     * @param $data
     * @throws Error
     * @throws \Delight\FileUpload\Throwable\InputNotSpecifiedError
     * @throws \Delight\FileUpload\Throwable\TempDirectoryNotFoundError
     * @throws \Delight\FileUpload\Throwable\TempFileWriteError
     * @throws \Delight\FileUpload\Throwable\UploadCancelledException
     * @throws \Error
     * @throws \Exception
     */

    public function process(string $type, $data)
    {

        if (GOOGLE_ENABLED)
            $this->addRecaptcha();

        if( $type == MVC_REQUEST_POST )
        {

            if( $this->check( $data->request, false ) == false )
            {

                $this->model->formError(FORM_ERROR_MISSING, "Please fill out all the missing fields");
            }
            else
            {

                $form = $this->pickKeys( $data->request );

                if( empty( $form->name ) )
                    $this->model->formError(FORM_ERROR_MISSING,"Please fill out all the missing fields");

                if( empty( $form->privacy ) )
                    $this->model->formError(FORM_ERROR_MISSING,"Please fill out all the missing fields");

                if( GOOGLE_ENABLED )
                {

                    if ($this->checkRecaptcha($form) == false) {

                        $this->model->formError(FORM_ERROR_GENERAL, "Google response invalid");
                        return;
                    }
                }

                $result = $this->verify( $form );

                if( is_array( $result ) )
                    $this->model->formError($result['type'],$result['value']);
                else
                {

                    try
                    {

                        $result = $this->upload( $form );
                    }
                    catch ( \Error $error )
                    {

                        $this->model->formError( FORM_ERROR_GENERAL, $error->getMessage() );
                        return;
                    }

                    $streams = [
                        $result["type"] => $result["key"]
                    ];

                    $trackid = $this->track->create(
                        $result['userid'],
                        $streams, $form->name,
                        $this->track->getMetadataArray(
                            null,
                            $this->markdown->markup( $form->description ),
                            [], null, $result['type'] )
                    );


                    if( $this->track->find( $form->name ) == false )
                        throw new Error("Failed to add track");

                    $this->doPostUploadWaveform( $trackid, $result );

                    @unlink( COLOURSPACE_ROOT . $result["temp"] );
                    @unlink( COLOURSPACE_ROOT . "files/converted/" . $result["filename"] . ".wav");

                    $this->model->formMessage( FORM_MESSAGE_SUCCESS,"Success! Redirecting you to your tracks");
                    $this->model->redirect( COLOURSPACE_URL_ROOT . "tracks", 2 );
                }
            }
        }
    }
    /**
     * @param string $type
     * @param $data
     * @return bool
     */

    public function authentication(string $type, $data)
    {

        return parent::authentication($type, $data);
    }


    /**
     * @param $trackid
     * @param $result
     * @throws \Error
     */

    public function doPostUploadWaveform( $trackid, $result )
    {

        $media = new MediaOperator( $result["temp"] );
        $wave = $media->getWaveform();

        $this->saveWaveform( $wave, $result['filename'] . ".png", $trackid, UPLOADS_WAVEFORMS_LOCAL );

        if( UPLOADS_WAVEFORMS_LOCAL )
            $path = "files/waveforms/" . $result['filename'] . ".png";
        else
            $path = AMAZON_BUCKET_URL . $result['filename'] . ".png";

        $this->track->updateMetadata( $trackid, [
            "waveform" =>  $path
        ]);
    }

    /**
     * @param $form
     * @return array
     * @throws Error
     * @throws \Delight\FileUpload\Throwable\InputNotSpecifiedError
     * @throws \Delight\FileUpload\Throwable\TempDirectoryNotFoundError
     * @throws \Delight\FileUpload\Throwable\TempFileWriteError
     * @throws \Delight\FileUpload\Throwable\UploadCancelledException
     * @throws \Error
     * @throws \ErrorException
     */

    private function upload( $form )
    {

        $result = null;

        try
        {

            $user = $this->user->get( $this->session->userid() );
            $limits = $this->getGroupLimits();
            $this->uploads->setHeader();

            if( $limits !== null )
            {

                if( $limits[ GROUPS_FLAG_MAXSIZE ] != -1 )
                    $this->uploads->setMaxFilesize( $limits[ GROUPS_FLAG_MAXSIZE ] );

                $allowed=[];

                if( $limits[ GROUPS_FLAG_LOSSLESS ] )
                    $allowed = [
                        "mp3", "wav", "flac"
                    ];
                else
                    $allowed = [
                        "mp3"
                    ];

                $this->uploads->setAllowedExtensions( $allowed );
            }
            else
                $this->uploads->setAllowedExtensions( ["mp3"] );

            $this->uploads->setFileName( $this->generate() );
            $result = $this->uploads->save();

            if( is_int( $result ) )
            {

                $errors = [
                    UPLOADS_ERROR_NOT_FOUND => "File not found",
                    UPLOADS_ERROR_FILENAME  => "Filename is invalid",
                    UPLOADS_ERROR_TOO_LARGE => "File is over the permitted maximum allowance",
                    UPLOADS_ERROR_EXTENSION => "Files extension is not permissed",
                    UPLOADS_ERROR_CANCELLED => "Upload was cancelled",
                ];

                throw new \Error( $errors[ $result ] );
            }

            $this->put( $user->userid, $form->name, $result->getFilenameWithExtension(), $result->getPath(), null, [], $this->getContentType( $result->getExtension() ));

            if( $this->amazon->exists( AMAZON_S3_BUCKET, $result->getFilenameWithExtension() ) == false )
                throw new \Error("Amazon failed to put object in bucket");

            $return = [
                "userid"    => $user->userid,
                "temp"      => UPLOADS_TEMPORARY_DIRECTORY . $result->getFilenameWithExtension(),
                "type"      => $result->getExtension(),
                "filename"  => $result->getFilename(),
                "key"       => $result->getFilenameWithExtension()
            ];

            return( $return );
        }
        catch ( \ErrorException $error )
        {

            if( $result == null || is_int( $result ) )
                throw $error;
            else
            {

                @unlink( $result->getPath() );
                throw $error;
            }
        }
    }

    /**
     * @param $userid
     * @param $trackname
     * @param $filename
     * @param $sourcefile
     * @param null $bucket
     * @param array $metadata
     * @param null $content_type
     */

    private function put( $userid, $trackname, $filename, $sourcefile, $bucket=null, $metadata=[], $content_type = null )
    {

        $metadata = array_merge( $metadata, [
            "userid"        => $userid,
            "trackname"     => $trackname,
            "uploadtime"    => Format::timestamp(),
            "ipaddress"     => $_SERVER["REMOTE_ADDR"]
        ]);

        if( $bucket == null )
            $bucket = AMAZON_S3_BUCKET;

        $this->amazon->put( $bucket, $filename, $sourcefile, $metadata, $content_type );
    }

    /**
     * @param $type
     * @return mixed
     */

    private function getContentType( $type )
    {

        $types = [
            "mp3"   => "audio/mp3",
            "wav"   => "audio/wav",
            "flac"  => "audio/flac"
        ];

        return( $types[ $type ] );
    }

    /**
     * @param $wave
     * @param $filename
     * @param $trackid
     * @param bool $local
     */

    private function saveWaveform( $wave, $filename, $trackid, $local=true )
    {

        if( file_exists( COLOURSPACE_ROOT . "files/waveforms/") == false )
            mkdir( COLOURSPACE_ROOT . "files/waveforms/");

        $path = "files/waveforms/" . $filename;

        if( $local == true )
            $wave->save( COLOURSPACE_ROOT . $path );
        else
        {

            $wave->save( COLOURSPACE_ROOT . $path );
            $this->amazon->put( AMAZON_S3_BUCKET, $filename, COLOURSPACE_ROOT . $path, ["trackid" => $trackid], "image/png" );

            unlink( COLOURSPACE_ROOT . $path  );
        }
    }

    /**
     * @param $path
     * @param bool $svg
     * @return mixed
     * @throws \Error
     */

    private function generateWaveform( $path, $svg=true )
    {

        $waveform = new WaveForm( $path );

        if( $svg )
            return( $waveform->svg() );
        else
            return( $waveform->generate() );
    }

    /**
     * @return string
     */

    private function generate()
    {

        return( uniqid(rand(), true) );
    }

    /**
     * @return array|null
     * @throws \Error
     */

    private function getGroupLimits()
    {

        $user = $this->user->get( $this->session->userid() );

        if( $this->group->has( $user->group ) == false )
            return null;

        $group = json_decode( json_encode( $this->group->get( $user->group ) ), true );

        try
        {
            return([
                GROUPS_FLAG_MAXLENGTH => $group["flags"][ GROUPS_FLAG_MAXLENGTH ],
                GROUPS_FLAG_MAXSIZE => $group["flags"][ GROUPS_FLAG_MAXSIZE ],
                GROUPS_FLAG_LOSSLESS => $group["flags"][ GROUPS_FLAG_LOSSLESS ]
            ]);
        }
        catch ( \Error $error )
        {

            return null;
        }
    }

    /**
     * @param $form
     * @return array|bool
     */

    private function verify( $form )
    {

        if( $this->checkName( $form->name ) == false )
            return([
                "type" => FORM_ERROR_INCORRECT,
                "value" => "Your name cannot contain any special characters"
            ]);

        if( strlen( $form->name ) > TRACK_NAME_MAXLENGTH )
            return([
                "type" => FORM_ERROR_INCORRECT,
                "value" => "Your name is too long! Shorten it down below 64 characters."
            ]);

        if( $this->track->find( $form->name )->isNotEmpty() )
            return([
                "type" => FORM_ERROR_INCORRECT,
                "value" => "Name is already taken, track names need to be unique!"
            ]);

        if( $form->privacy != ( TRACK_PRIVACY_PUBLIC || TRACK_PRIVACY_PRIVATE || TRACK_PRIVACY_PERSONAL ) )
            return([
                "type" => FORM_ERROR_INCORRECT,
                "value" => "Unknown privacy type"
            ]);

        return true;
    }

    /**
     * @param $name
     * @return bool
     */

    private function checkName( $name )
    {

        if( preg_match("'/[\'^£$%&*()}{@#~?><>,|=_+¬-]/'", $name ) )
            return false;

        return true;
    }
}