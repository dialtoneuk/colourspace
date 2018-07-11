<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 15:19
 */

namespace Colourspace\Framework\Controllers;


use Colourspace\Framework\Controller;
use Colourspace\Container;
use Colourspace\Framework\Recaptcha;
use Colourspace\Framework\User;
use Colourspace\Framework\Util\Collector;
use Colourspace\Framework\Util\Format;
use Colourspace\Framework\Session;
use League\OAuth2\Client\Provider\Google;

class Login extends Controller
{

    /**
     * @var User
     */

    protected $user;

    /**
     * @var Session
     */

    protected $session;


    /**
     * @return array
     */

    public function keyRequirements()
    {

        $array = [
            "email",
            "password"
        ];

        if( GOOGLE_ENABLED )
            $array[] = "g-recaptcha-response";

        return( $array );
    }

    /**
     * @param string $type
     * @param $data
     * @throws \Error
     * @throws \Exception
     */

    public function process(string $type, $data)
    {

        if( GOOGLE_ENABLED )
            $this->addRecaptcha();

        if( $type == MVC_REQUEST_POST )
        {

            if( $this->check( $data->request ) == false )
            {

                $this->model->formError(FORM_ERROR_MISSING,"Please fill out all the missing fields");
            }
            else
            {

                $form = $this->pickKeys( $data->request, false  );

                if( GOOGLE_ENABLED )
                {

                    if( $this->checkRecaptcha( $form ) == false )
                    {

                        $this->model->formError( FORM_ERROR_GENERAL, "Google response invalid");
                        return;
                    }
                }

                $result = $this->checkLogin( $form );

                if( is_array( $result ) )
                    $this->model->formError($result['type'],$result['value']);
                else
                {

                    if( is_int( $result ) == false )
                        throw new \Error("Incorrect userid type");

                    $this->session->Login( $result );

                    if( $this->session->isLoggedIn()  == false )
                        throw new \Error("Failed to login");

                    $this->model->formMessage( FORM_MESSAGE_SUCCESS,"Success! Redirecting you in a few...");
                    $this->model->redirect( COLOURSPACE_URL_ROOT, 2 );
                }
            }
        }
    }

    /**
     * Returns an array on error, int of the userid on success
     *
     * @param $form
     * @return array|int
     */

    public function checkLogin( $form )
    {

        if( $this->user->hasEmail( $form->email ) == false )
            return([
                "type" => FORM_ERROR_INCORRECT,
                "value" => "Email invalid, please try again or reset your password"
            ]);

        $user = $this->user->getByEmail( $form->email );

        if( $this->checkPassword( $form->password, $user->password, $user->salt ) == false  )
            return([
                "type" => FORM_ERROR_INCORRECT,
                "value" => "Email invalid, please try again or reset your password"
            ]);

        return $user->userid;
    }

    /**
     * @throws \Error
     */

    public function before()
    {

        parent::before();

        $this->user = Collector::new("User");
        $this->session = Container::get('application')->session;
    }

    /**
     * @param string $type
     * @param $data
     * @return bool
     * @throws \Error
     */

    public function authentication(string $type, $data)
    {

        if( empty( $this->session ) )
            $session = Container::get('application')->session;
        else
            $session = $this->session;

        if( $session->isLoggedIn() )
            return false;

        return true;
    }

    /**
     * @param $given_password
     * @param $encrypted_password
     * @param $salt
     * @return bool
     */

    private function checkPassword( $given_password, $encrypted_password, $salt )
    {

        if( Format::saltedPassword( $salt, $given_password ) == $encrypted_password )
        {

            return true;
        }

        return false;
    }
}