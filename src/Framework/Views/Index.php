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

    public function get()
    {

        return([
            'index.php',
            $this->model->toArray()
        ]);
    }
}