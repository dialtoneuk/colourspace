<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 06/07/2018
 * Time: 01:16
 */

namespace Colourspace\Database\Tables;

use Colourspace\Database\Table;

class Blogs extends Table
{

    /**
     * @return string
     */

    public function name()
    {

        return "blog";
    }

    /**
     * The map for the users table
     *
     * @return array
     */

    public function map()
    {

        return [
            'blogid'    => FIELD_TYPE_INCREMENTS,
            'userid' => FIELD_TYPE_STRING,
            'content' => FIELD_TYPE_JSON,
            'creation'  => FIELD_TYPE_TIMESTAMP
        ];
    }

    /**
     * @param $blogid
     * @return \Illuminate\Support\Collection
     */

    public function get( $blogid )
    {

        return( $this->query()->where(["blogid" => $blogid ] )->get() );
    }

    /**
     * @param $blogid
     * @return bool
     */

    public function has( $blogid )
    {

        return( $this->query()->where(["blogid" => $blogid ])->get()->isNotEmpty() );
    }

    /**
     * @param $blogid
     */

    public function remove( $blogid )
    {

        $this->query()->where(["blogid" => $blogid ])->delete();
    }
}