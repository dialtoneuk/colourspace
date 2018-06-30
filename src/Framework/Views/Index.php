<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 30/06/2018
 * Time: 16:53
 */

namespace Colourspace\Framework\Views;


use Colourspace\Framework\View;

class Index extends View
{

    /**
     * @return array
     */

    public function get()
    {

        //You do not need to append .php onto the end of the file for it to be read
        return([
            'index',
            $this->model->toArray()
        ]);
    }
}