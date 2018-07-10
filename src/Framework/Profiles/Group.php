<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 01/07/2018
 * Time: 00:50
 */

namespace Colourspace\Framework\Profiles;


use Colourspace\Framework\Profile;
use Colourspace\Container;
use Colourspace\Framework\User as UserClass;
use Colourspace\Framework\Group as GroupClass;

class Group extends Profile
{

    /**
     * @var UserClass
     */

    protected $user;
    /**
     * @var GroupClass
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
     * @return null|void
     * @throws \Error
     */

    public function create()
    {
        if( $this->isLoggedIn() == false )
            $this->objects = null;
        else
        {

            $user = $this->user->get( Container::get('application')->session->userid() );

            if( $this->group->has($user->group ) == false )
                $this->objects = null;
            else
            {

                $group = $this->group->get( $user->group );

                $this->objects = [
                    'name' => $group["name"],
                    'admin' => $group["flags"]["admin"],
                    GROUPS_FLAG_MAXLENGTH => $group["flags"][ GROUPS_FLAG_MAXLENGTH ],
                    GROUPS_FLAG_MAXSIZE => $group["flags"][ GROUPS_FLAG_MAXSIZE ]
                ];
            }
        }

        parent::create();
    }
}