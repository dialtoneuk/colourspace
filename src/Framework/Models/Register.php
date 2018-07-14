<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 03/07/2018
 * Time: 16:41
 */

namespace Colourspace\Framework\Models;


use Colourspace\Framework\TemporaryUsername;
use Colourspace\Framework\Util\Collector;

class Register extends DefaultModel
{

    /**
     * @var TemporaryUsername
     */

    protected $temporaryusername;

    /**
     * @param bool $doprofiles
     * @throws \Error
     */

    public function startup( $doprofiles=true )
    {

        //Disable do profiles if you are adding profiles after this line
        parent::startup( $doprofiles );

        $this->temporaryusername = Collector::new("TemporaryUsername");

        if( $this->temporaryusername->has( session_id() ) )
            $this->object->temporaryusername = $this->temporaryusername->get( session_id() );
    }
}