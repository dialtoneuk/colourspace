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
use Colourspace\Framework\Util\Format;
use Colourspace\Framework\Session;

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
     * @var Recaptcha
     */

    protected $recaptcha;

    /**
     * @param string $type
     * @param $data
     * @throws \Error
     * @throws \Exception
     */

    public function process(string $type, $data)
    {

        if (GOOGLE_ENABLED)
            $this->model->recaptcha = [
                'script' => $this->recaptcha->script(),
                'html' => $this->recaptcha->html()
            ];

        /**
         * On Post
         */

        if( $type == MVC_REQUEST_POST )
        {

            if( empty( $data['data'] ) )
                return;

            if( $this->check( $data['data'] ) == false )
            {

                $this->model->formError(FORM_ERROR_MISSING,"Please fill out all the missing fields");
                return;
            }

            $form = $this->pickKeys( $data['data'] );

            if( GOOGLE_ENABLED )
            {

                if( $this->recaptcha->isValid( $form->recaptcha ) == false )
                {
                    $this->model->formError(FORM_ERROR_GENERAL,"Recaptcha response is invalid");
                    return;
                }
            }

            if( $this->user->hasEmail( $form->email ) == false )
            {

                $this->model->formError(FORM_ERROR_INCORRECT,"Email invalid, please try again or reset your password");
                return;
            }

            $user = $this->user->getByEmail( $form->email );

            if( $this->checkPassword( $form->password, $user->password, $user->salt ) == false  )
            {

                $this->model->formError(FORM_ERROR_INCORRECT,"Email invalid, please try again or reset your password");
                return;
            }

            $this->session->Login( $user->userid );

            if( $this->session->isLoggedIn()  == false )
            {

                $this->model->formError(FORM_ERROR_GENERAL,"Failed to login");
                return;
            }

            $this->model->formMessage( FORM_MESSAGE_SUCCESS,"Successfully logged in");
        }
    }

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
     * @throws \Error
     */

    public function before()
    {

        $this->user = new User();
        $this->session = Container::get('application')->session;

        if( GOOGLE_ENABLED )
            $this->recaptcha = new Recaptcha();


        parent::before();
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