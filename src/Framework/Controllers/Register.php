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
use Colourspace\Framework\TemporaryUsername;
use Colourspace\Framework\User;

class Register extends Controller
{

    /**
     * @var User
     */

    protected $user;

    /**
     * @var TemporaryUsername
     */

    protected $temporaryusername;

    /**
     * @var Recaptcha
     */

    protected $recaptcha;

    /**
     * Annoyingly strict passwords
     *
     * @var array
     */

    protected $password_strict_stems = [
        "@[A-Z]@",
        "@[a-z]@",
        "@[0-9]@"
    ];

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

            if( $this->user->hasEmail( $form->email ) )
            {

                $this->model->formError(FORM_ERROR_INCORRECT,"Email has already been taken");
                return;
            }

            if( $this->temporaryusername->has( session_id( ) ) == false )
                $username = $this->temporaryusername->generate();
            else
                $username = $this->temporaryusername->get( session_id() );

            if( $this->checkPassword( $form->password, $form->confirm_password ) == false )
            {

                if( ACCOUNT_PASSWORD_STRICT )
                    $this->model->formError(FORM_ERROR_INCORRECT,"Either your passwords do not match, or they do not meet the requirements set by the administrator");
                else
                    $this->model->formError(FORM_ERROR_INCORRECT,"Either your passwords do not match, or they do not meet the requirements set by the administrator. They need to contain a capital letter, special character, a number and be above " . ACCOUNT_PASSWORD_MIN . " characters.");
                return;
            }
            
            try
            {
                $this->user->register($username, $form->email, $form->password );
            }
            catch (\Error $e)
            {

                $this->model->formError( FORM_ERROR_GENERAL, "For some reason, your register attempt has failed. Please tell a developer as this probably shouldn't have happened");
                return;
            }

            $this->model->formMessage( FORM_MESSAGE_SUCCESS, "Account created! Please login using your email and password. Don't forget to verify your account!");
        }
    }

    /**
     * @throws \Error
     */

    public function before()
    {

        $this->user = new User();
        $this->temporaryusername = new TemporaryUsername();

        if( GOOGLE_ENABLED )
            $this->recaptcha = new Recaptcha();

        parent::before();
    }

    /**
     * @return array
     */

    public function keyRequirements()
    {

      $array = [
          "email",
          "password",
          "confirm_password"
      ];

      if( GOOGLE_ENABLED )
          $array[] = "g-recaptcha-response";

      return( $array );
    }

    /**
     * @param string $type
     * @param $data
     * @return bool
     * @throws \Error
     */

    public function authentication(string $type, $data)
    {

        $application = Container::get('application');

        if( $application->session->isLoggedIn() )
            return false;

        return true;
    }

    /**
     * @param $password
     * @param $confirm_password
     * @return bool
     */

    private function checkPassword( $password, $confirm_password )
    {

        if( $password !== $confirm_password )
            return false;

        if( strlen( $password  ) < ACCOUNT_PASSWORD_MIN )
            return false;

        if( ACCOUNT_PASSWORD_STRICT )
        {

            foreach( $this->password_strict_stems as $stem )
            {

                if( !preg_match( $stem, $password ) )
                    return false;
            }

            return true;
        }
        else
            return true;
    }
}