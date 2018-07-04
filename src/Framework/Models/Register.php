<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 03/07/2018
 * Time: 16:41
 */

namespace Colourspace\Framework\Models;


use Colourspace\Framework\TemporaryUsername;

class Register extends DefaultModel
{

    /**
     * @var TemporaryUsername
     */

    protected $temporaryusername;

    /**
     * @throws \Error
     */

    public function startup()
    {

        parent::startup();

        $this->temporaryusername = new TemporaryUsername();

        if( $this->temporaryusername->has( session_id() ) )
        {

            $this->object->temporaryusername = $this->temporaryusername->get( session_id() );
        }
    }
}