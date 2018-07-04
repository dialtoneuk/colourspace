<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 16:49
 */

namespace Colourspace\Framework\Models;


use Colourspace\Container;
use Colourspace\Framework\Profiles\Group;
use Colourspace\Framework\Model;
use Colourspace\Framework\Profiles\User;

class DefaultModel extends Model
{

    /**
     * @var User
     */

    protected $profiles = [];

    /**
     * @throws \Error
     */

    public function startup()
    {

        parent::startup();

        if( Container::get('application')->session->isLoggedIn() == false )
            return;

        $this->profiles = [
            'user' => new User(),
            'group' => new Group()
        ];

        $this->profiles['user']->create();
        $this->profiles['group']->create();

        $this->object->user = $this->profiles['user']->get();
        $this->object->group = $this->profiles['group']->get();
    }
}