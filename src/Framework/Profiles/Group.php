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

        $this->user = $this->getClass('User');
        $this->group = $this->getClass('Group');
    }

    /**
     * @return null|void
     * @throws \Error
     */

    public function create()
    {

        //If we aren't logged in
        if( $this->isLoggedIn() == false )
        {

            $this->objects = null;
            return;
        }

        $user = $this->user->get( Container::get('application')->session->userid() );

        if( $this->group->has($user->group ) == false )
        {

            $this->objects = null;
            return;
        }

        $group = $this->group->get( $user->group );

        $this->objects = [
            'name' => $group->name,
            'admin' => $group->permissions->admin,
            'uploadtime' => $group->permissions->uploadtime
        ];

        parent::create();
    }
}