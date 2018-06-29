<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 29/06/2018
 * Time: 23:35
 */

namespace Colourspace\Database;
use Colourspace\Container;
use Colourspace\Interfaces\TableInterface;

class Table implements TableInterface
{

    /**
     * @var \Illuminate\Database\Connection
     */
    private $connection;

    /**
     * Table constructor.
     * @throws \Error
     */

    public function __construct()
    {

        if ( Container::has('application') == false )
            throw new \Error('Application not initialized');

        //Get current connection from application
        $this->connection = Container::get('application')->connection->connection;
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */

    public function query()
    {

        return( $this->connection->table( $this->name() ) );
    }

    /**
     * @param $values
     * @return bool
     */

    public function verify( $values )
    {

        foreach ( $this->map() as $key=>$value )
        {

            if( $value == LARAVEL_TYPE_INCREMENTS )
                continue;

            if( isset( $values[ $key ] ) == false )
                return false;

            switch ( $value )
            {

                case( LARAVEL_TYPE_STRING ):
                    if ( is_string( $value[ $key ] ) == false )
                        return false;
                    break;
                case( LARAVEL_TYPE_INT ):
                    if ( is_int( $value[ $key ] ) == false )
                        return false;
                    break;
            }
        }

        return true;
    }

    /**
     * All records
     *
     * @return \Illuminate\Support\Collection
     */

    public function all()
    {

        return( $this->query()->get() );
    }

    /**
     * Picks
     *
     * @param int $number
     * @return \Illuminate\Support\Collection
     */

    public function pick( $number=42 )
    {

        return( $this->query()->take( $number )->get() );
    }

    /**
     * A map used for migration, also used when verifying inserts
     *
     * @return array
     */

    public function map()
    {

        return [
            'userid' => LARAVEL_TYPE_INCREMENTS,
            'username' => LARAVEL_TYPE_STRING,
            'email' => LARAVEL_TYPE_STRING,
            'password' => LARAVEL_TYPE_STRING
        ];
    }

    /**
     * The name of the table
     *
     * @return string
     */

    public function name()
    {

        if ( get_called_class() )
            return strtolower( get_called_class() );

        return "table";
    }
}