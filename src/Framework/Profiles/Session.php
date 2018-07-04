<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 04/07/2018
 * Time: 01:24
 */

namespace Colourspace\Framework\Profiles;

use Colourspace\Container;
use Colourspace\Framework\Profile;

class Session extends Profile
{

    /**
     * @var \Colourspace\Framework\Session
     */

    protected $session;

    /**
     * Session constructor.
     * @param array $classes
     * @throws \Error
     */

    public function __construct(array $classes = [])
    {

        parent::__construct($classes);
        $this->session = Container::get('application')->session;
    }

    /**
     * @return null|void
     * @throws \Error
     */

    public function create()
    {

        $this->objects = [
            "loggedin"  => $this->session->isLoggedIn(),
            "active"    => $this->session->isActive(),
            "sessionid" => session_id()
        ];

        if( $this->isLoggedIn() )
        {

            $this->objects['userid'] = $this->session->userid();
        }
    }
}