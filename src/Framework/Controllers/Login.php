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
use Colourspace\Framework\User;

class Login extends Controller
{

    /**
     * @var User
     */

    protected $user;

    /**
     * @param string $type
     * @param $data
     * @throws \Error
     */

    public function process(string $type, $data)
    {

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

            if( $this->user->hasEmail( $form->email ) == false )
            {

                $this->model->formError(FORM_ERROR_INCORRECT,"Email or password invalid, please try again or reset your password");
                return;
            }

            $user = $this->user->getByEmail( $form->email );

            if( $this->checkPassword( $form->password, $user->password, $user->salt ) == false )
            {

                $this->model->formError(FORM_ERROR_INCORRECT,"Email or password invalid, please try again or reset your password");
                return;
            }

            Container::get('application')->session->Login( $user->userid );

            if( Container::get('application')->session->isLoggedIn()  == false )
            {

                $this->model->formError(FORM_ERROR_GENERAL,"Email or password invalid, please try again or reset your password");
                return;
            }

            $this->model->formMessage( FORM_MESSAGE_SUCCESS,"Successfully logged in");
        }

        /**
         * On delete
         */

        if( $type == MVC_REQUEST_DELETE )
        {

            if( Container::get('application')->session->isLoggedIn()  == false )
            {

                $this->model->formError(FORM_ERROR_GENERAL,"You need to be logged in to logout");
                return;
            }

            Container::get('application')->session->destroy();

            $this->model->formMessage( FORM_MESSAGE_SUCCESS,"You have been logged out");
        }
    }

    /**
     * @return array
     */

    public function keyRequirements()
    {

        return ([
            "email",
            "password"
        ]);
    }

    /**
     * @throws \Error
     */

    public function before()
    {

        $this->user = new User();

        //Do parent call
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

        if ( Container::has('application') == false )
            throw new \Error('Application has not been initialized');

        $application = Container::get('application');

        if( $application->session->isLoggedIn() )
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

        if( sha1( $given_password . $salt ) != $encrypted_password )
            return false;

        return true;
    }
}