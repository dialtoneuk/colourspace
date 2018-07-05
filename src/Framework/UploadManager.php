<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 05/07/2018
 * Time: 20:43
 */

namespace Colourspace\Framework;

use Delight\FileUpload\FileUpload;
use Delight\FileUpload\Throwable\FileTooLargeException;
use Delight\FileUpload\Throwable\InputNotFoundException;
use Delight\FileUpload\Throwable\InvalidExtensionException;
use Delight\FileUpload\Throwable\InvalidFilenameException;
use Delight\FileUpload\Throwable\UploadCancelledError;

class UploadManager
{

    /**
     * @var FileUpload
     */

    protected $upload;

    /**
     * UploadManager constructor.
     * @param bool $auto_initialize
     */

    public function __construct($auto_initialize = true)
    {

        $this->upload = new FileUpload();

        if ($auto_initialize)
            $this->initialize();
    }

    public function initialize()
    {

        $this->checkDir();
        $this->upload->withTargetDirectory(UPLOADS_TEMPORARY_DIRECTORY);
    }

    /**
     * @param array $extensions
     */

    public function setAllowedExtensions(array $extensions = ["mp3"])
    {

        $this->upload->withAllowedExtensions($extensions);
    }

    /**
     * @param null $filename
     */

    public function setFileName( $filename=null )
    {

        if( $filename == null )
            $filename = $this->generate();

        $this->upload->withTargetFilename( $filename );
    }

    /**
     * @param int $megabytes
     */

    public function setMaxFilesize( int $megabytes )
    {

        $this->upload->withMaximumSizeInMegabytes( $megabytes );
    }

    /**
     * @param null $header
     */

    public function setHeader( $header=null )
    {

        if( $header=null )
            $header = UPLOADS_POST_KEY;

        $this->upload->from( $header );
    }

    /**
     * @return \Delight\FileUpload\File
     * @throws \Delight\FileUpload\Throwable\Error
     * @throws \Delight\FileUpload\Throwable\InputNotSpecifiedError
     * @throws \Delight\FileUpload\Throwable\TempDirectoryNotFoundError
     * @throws \Delight\FileUpload\Throwable\TempFileWriteError
     * @throws \Delight\FileUpload\Throwable\UploadCancelledException
     */

    public function save()
    {

        try
        {

            return( $this->upload->save() );
        }
        catch ( InputNotFoundException  $e) {
            return( UPLOADS_ERROR_NOT_FOUND );
        }
        catch ( InvalidFilenameException $e) {
            return( UPLOADS_ERROR_FILENAME );
        }
        catch ( InvalidExtensionException  $e) {
            return( UPLOADS_ERROR_EXTENSION );
        }
        catch ( FileTooLargeException $e) {
            return( UPLOADS_ERROR_TOO_LARGE );
        }
        catch ( UploadCancelledError $e) {
            return( UPLOADS_ERROR_CANCELLED );
        }
    }

    /**
     * Generates a random file name
     *
     * @return string
     */

    private function generate()
    {

        return( base64_encode( openssl_random_pseudo_bytes( 32 ) ) );
    }

    /**
     * Creates dir if it don't exist
     */

    private function checkDir()
    {

        if( file_exists( COLOURSPACE_ROOT . UPLOADS_TEMPORARY_DIRECTORY ) == false )
            mkdir( COLOURSPACE_ROOT . UPLOADS_TEMPORARY_DIRECTORY );
    }
}