<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 22:49
 */

namespace Colourspace\Framework\Profiles;


use Colourspace\Container;
use Colourspace\Framework\Group;
use Colourspace\Framework\Profile;
use Colourspace\Framework\User as UserClass;

class User extends Profile
{

    /**
     * @var UserClass
     */

    protected $user;
    /**
     * @var Group
     */
    protected $group;

    /**
     * User constructor.
     * @throws \Error
     */

    public function __construct()
    {

        parent::__construct(
            [
                [
                    "Colourspace\\Framework\\", "User"
                ],
                [
                    "Colourspace\\Framework\\", "Group"
                ]
            ]

        );

        $this->user = $this->class('User');
        $this->group = $this->class('Group');
    }

    /**
     * @throws \Error
     */

    public function create()
    {

        if( $this->isLoggedIn() == false )
            $this->objects = null;
        else
        {

            $user = $this->user->get( Container::get('application')->session->userid() );

            $this->objects = [
                'username' => $user->username,
                'userid'    => $user->userid,
                'colour'     => "#" . $user->colour,
                'email'    => $user->email
            ];
        }

        parent::create();
    }
}