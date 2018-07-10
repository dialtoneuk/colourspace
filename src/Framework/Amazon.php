<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 05/07/2018
 * Time: 23:37
 */

namespace Colourspace\Framework;

use Aws\S3\S3Client;
use Colourspace\Framework\Util\Format;

class Amazon
{

    /**
     * @var S3Client
     */

    protected $client;

    /**
     * Amazon constructor.
     * @throws \Error
     */

    public function __construct()
    {

        $credentials = $this->getCredentials();

        $this->client = S3Client::factory([
            "credentials" => [
                'key' => $credentials->key,
                'secret' => $credentials->secret,
            ],
            'region' => AMAZON_LOCATION_EU_WEST_2,
            'version' => 'latest'
        ]);
    }

    /**
     * @param $bucket
     * @param $filename
     * @param $sourcefile
     * @param array $metadata
     * @return \Aws\Result
     */

    public function put( $bucket, $filename, $sourcefile, $metadata=[] )
    {

        $contents = file_get_contents( $sourcefile );

        $result = $this->client->putObject(array(
            'Bucket'     => $bucket,
            'Key'        => $filename,
            'body' => $contents,
            'Metadata'   => $metadata
        ));

        $this->client->waitUntil('ObjectExists', array(
            'Bucket' => $bucket,
            'Key'    => $filename
        ));

        return( $result );
    }

    /**
     * @param $bucket
     * @param $filename
     * @return bool
     */

    public function exists( $bucket, $filename )
    {

        if( $this->client->doesObjectExist( $bucket, $filename ) == false )
            return false;

        return true;
    }

    /**
     * @param string $bucket
     * @return bool
     */

    public function bucketExists( string $bucket )
    {

        if( $this->client->doesBucketExist( $bucket ) == false )
            return false;

        return true;
    }

    /**
     * @param string $bucket
     * @param string $region
     */

    public function createBucket( string $bucket, string $region )
    {

        $this->client->createBucket([
            'Bucket' => $bucket,
            'LocationConstraint' => $region
        ]);

        $this->client->waitUntil('BucketExists', array('Bucket' => $bucket));
    }

    /**
     * @return mixed
     * @throws \Error
     */

    public function getCredentials()
    {

        if( file_exists( COLOURSPACE_ROOT . "config/" . AMAZON_CREDENTIALS_FILE ) == false )
            throw new \Error("Incorrect credentials");

        return( json_decode( file_get_contents( COLOURSPACE_ROOT . "config/" . AMAZON_CREDENTIALS_FILE ) ) );

    }
}