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
use Colourspace\Framework\Util\Format;
use Delight\FileUpload\Throwable\Error;
use flight\net\Request;

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
     * @throws \Error
     */

    public function keyRequirements()
    {

        $array = [
            UPLOADS_POST_KEY,
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

            if( $this->check( $data->request ) == false )
                $this->model->formError(FORM_ERROR_MISSING,"Please fill out all the missing fields");
            else
            {

                $form = $this->pickKeys( $data->request );

                if( GOOGLE_ENABLED )
                {

                    if( $this->checkRecaptcha( $form ) == false )
                    {

                        $this->model->formError( FORM_ERROR_GENERAL, "Google response invalid");
                        return;
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

                        $this->track->create(
                            $result['userid'],
                            $streams, $form->name,
                            $this->track->getMetadataArray( null, $form->description, [], null )
                        );

                        if( $this->track->find( $form->name ) == false )
                            throw new Error("Failed to add track");

                        $this->model->formMessage( FORM_MESSAGE_SUCCESS, "Track uploaded! Redirecting you in a few");
                        $this->model->redirect(COLOURSPACE_URL_ROOT . "tracks/" . $form->name, 3 );
                    }
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
     * @param $form
     * @return array
     * @throws \Delight\FileUpload\Throwable\Error
     * @throws \Delight\FileUpload\Throwable\InputNotSpecifiedError
     * @throws \Delight\FileUpload\Throwable\TempDirectoryNotFoundError
     * @throws \Delight\FileUpload\Throwable\TempFileWriteError
     * @throws \Delight\FileUpload\Throwable\UploadCancelledException
     * @throws \Error
     */

    private function upload( $form )
    {

        $user = $this->user->get( $this->session->userid() );

        $limits = $this->getGroupLimits();

        if( $limits !== null )
        {

            if( $limits[ GROUPS_FLAG_MAXSIZE ] != -1 )
                $this->uploads->setMaxFilesize( $limits[ GROUPS_FLAG_MAXSIZE ] );

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
            throw new \Error( "Error");

        $this->put( $user->userid, $form->name, $result->getFilenameWithExtension(), $result->getPath() );

        if( $this->amazon->exists( AMAZON_S3_BUCKET, $result->getFilenameWithExtension() ) == false )
            throw new \Error("Amazon failed to put object in bucket");

       $return = [
           "userid" => $user->userid,
           "type"   => $result->getExtension(),
           "key"    => $result->getFilenameWithExtension()
        ];

        unlink( $result->getPath() );

        return( $return );
    }

    /**
     * @param $userid
     * @param $trackname
     * @param $filename
     * @param $sourcefile
     * @param null $bucket
     */

    private function put( $userid, $trackname, $filename, $sourcefile, $bucket=null )
    {

        if( $bucket == null )
            $bucket = AMAZON_S3_BUCKET;

        $this->amazon->put( $bucket, $filename, $sourcefile, [
            "userid"        => $userid,
            "trackname"     => $trackname,
            "uploadtime"    => Format::timestamp(),
            "ipaddress"     => $_SERVER["REMOTE_ADDR"]
        ]);
    }

    /**
     * @return string
     */

    private function generate()
    {

        return( base64_encode( openssl_random_pseudo_bytes(16) ) . time() );
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

        if( strlen( $form->name ) < TRACK_NAME_MAXLENGTH )
            return([
                "type" => FORM_ERROR_INCORRECT,
                "value" => "Your name is too long! Shorten it down below 64 characters."
            ]);

        if( $this->track->find( $form->name )->isNotEmpty() )
            return([
                "type" => FORM_ERROR_INCORRECT,
                "value" => "Name is already taken, track names need to be unique!"
            ]);

        if( $form->privacy !== ( TRACK_PRIVACY_PUBLIC || TRACK_PRIVACY_PRIVATE || TRACK_PRIVACY_PERSONAL ) )
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

        if( preg_match("/[\W]+/", $name ) == 0 )
            return false;

        return true;
    }
}