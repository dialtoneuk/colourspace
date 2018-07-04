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
use Colourspace\Framework\Util\Collector;

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
     * @throws \Error
     * @throws \Exception
     */

    public function process(string $type, $data)
    {

        if (GOOGLE_ENABLED)
            $this->addRecaptcha();

        if( $type == MVC_REQUEST_POST )
        {

            if( $this->check( $data['data'] ) == false )
            {

                $this->model->formError(FORM_ERROR_MISSING,"Please fill out all the missing fields");
            }
            else
            {

                $form = $this->pickKeys( $data['data'] );

                if( GOOGLE_ENABLED )
                    if( $this->checkRecaptcha( $form ) == false )
                    {

                        $this->model->formError( FORM_ERROR_GENERAL, "Google response invalid");
                        return;
                    }

                $result = $this->checkRegister( $form );

                if( is_array( $result ) )
                    $this->model->formError($result['type'],$result['value']);
                else
                {

                    $this->user->register( $this->getTemporaryUsername(), $form->email, $form->password );

                    if( $this->user->hasEmail( $form->email ) == false )
                        throw new \Error("Failed to register");

                    $this->model->formMessage( FORM_MESSAGE_SUCCESS, "Account registered! Redirecting you to the login page in a few...");
                    $this->model->redirect( COLOURSPACE_URL_ROOT, 2 );
                }
            }
        }
    }

    /**
     * @throws \Error
     */

    public function before()
    {

        parent::before();

        $this->user = Collector::new("User");
        $this->temporaryusername = Collector::new("TemporaryUsername");
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
     * @param $form
     * @return array|bool
     */

    private function checkRegister( $form )
    {

        if( $this->user->hasEmail( $form->email ) )
            return([
                "type" => FORM_ERROR_INCORRECT,
                "value" => "Email has already been taken"
            ]);

        if( $this->checkPassword( $form->password, $form->confirm_password ) == false )
            return([
                "type" => FORM_ERROR_GENERAL,
                "value" => "Your password is too weak, please revise."
            ]);

       return true;
    }

    /**
     * @return \Illuminate\Support\Collection|string
     */

    private function getTemporaryUsername()
    {

        if( $this->temporaryusername->has( session_id( ) ) == false )
            return $this->temporaryusername->generate();
        else
            return $this->temporaryusername->get( session_id() );
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