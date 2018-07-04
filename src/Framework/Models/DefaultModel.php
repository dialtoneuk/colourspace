<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 16:49
 */

namespace Colourspace\Framework\Models;


use Colourspace\Container;
use Colourspace\Framework\Profiles\Website;
use Colourspace\Framework\Model;

class DefaultModel extends Model
{

    /**
     * @var Website
     */

    protected $profile;

    /**
     * @throws \Error
     */

    public function startup()
    {

        $profile = new Website();
        $profile->create();

        $this->object->profiles = [
            "website" => $profile->toArray()
        ];

        parent::startup();
    }
}