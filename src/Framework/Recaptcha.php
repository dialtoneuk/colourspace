<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 03/07/2018
 * Time: 17:11
 */

namespace Colourspace\Framework;

use Phelium\Component\reCAPTCHA as Google;

class Recaptcha
{

    /**
     * @var Google
     */

    protected $recaptcha;

    /**
     * Recaptcha constructor.
     * @param null $site
     * @param null $secret
     */

    public function __construct( $site=null, $secret=null )
    {

        if( GOOGLE_ENABLED )
        {

            if( $site == null )
                $site = GOOGLE_SITE_KEY;

            if( $secret == null )
                $secret = GOOGLE_SITE_SECRET;

            $this->recaptcha = new Google();
            $this->recaptcha->setSiteKey( $site );
            $this->recaptcha->setSecretKey( $secret );
        }

    }

    /**
     * @param $response
     * @return bool
     * @throws \Exception
     */

    public function isValid( $response )
    {

        if( GOOGLE_ENABLED == false )
            return true;

        if( $this->recaptcha->isValid( $response ) )
            return true;

        return false;
    }

    /**
     * @return string
     */

    public function html()
    {

        if( GOOGLE_ENABLED == false )
            return null;

        return( $this->recaptcha->getHtml() );
    }

    /**
     * @return string
     */

    public function script()
    {

        if( GOOGLE_ENABLED == false )
            return null;

        return( $this->recaptcha->getScript() );
    }
}