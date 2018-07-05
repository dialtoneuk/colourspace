<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 29/06/2018
 * Time: 23:35
 */

namespace Colourspace\Database;
use Colourspace\Container;
use Colourspace\Database\Interfaces\TableInterface;

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
     * @param $column
     * @param $value
     * @return \Illuminate\Support\Collection
     */

    public function search( $column, $value )
    {

        return( $this->query()->where([ $column => $value ] )->get() );
    }

    /**
     * @param $values
     * @param bool $verify
     * @return int
     * @throws \Error
     */

    public function insert( $values, $verify=true )
    {

        if ( $verify )
        {
            if ( $this->verify( $values ) == false )
                throw new \Error('Values are incorrect for this table');
        }

        return( $this->query()->insertGetId( $values ) );
    }

    /**
     * @param $values
     * @return bool
     */

    public function verify( $values )
    {

        foreach ( $this->map() as $key=>$value )
        {

            if( $this->ignoreFields( $value ) )
                continue;

            if( isset( $values[ $key ] ) == false )
                return false;

            switch ( $value )
            {

                case( FIELD_TYPE_STRING ):
                    if ( is_string( $values[ $key ] ) == false )
                        return false;
                    break;
                case( FIELD_TYPE_INT ):
                    if ( is_int( $values[ $key ] ) == false )
                        return false;
                    break;
                case( FIELD_TYPE_IPADDRESS ):
                    if( filter_var( $values[ $key ], FILTER_VALIDATE_IP ) == false )
                        return false;
                    break;
                case( FIELD_TYPE_DECIMAL ):
                    if( is_float( $values[ $key ] ) == false )
                        return false;
                    break;
                case( FIELD_TYPE_JSON ):
                    json_decode( $values[ $key ] );
                    if( json_last_error() !== JSON_ERROR_NONE )
                        return false;
                    break;
                default:
                    return false;
                    break;
            }
        }

        return true;
    }

    /**
     * @param $field
     * @return bool
     */

    private function ignoreFields( $field )
    {

        $ignores = [
            FIELD_TYPE_INCREMENTS,
            FIELD_TYPE_PRIMARY,
            FIELD_TYPE_TIMESTAMP
        ];

        foreach( $ignores as $ignore )
        {

            if( $field == $ignore )
                return true;
        }

        return false;
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
     * @param array $where
     * @param array $values
     * @return int
     */

    public function update( array $where, array $values )
    {

        return( $this->query()->where( $where )->update( $values ) );
    }

    /**
     * A map used for migration, also used when verifying inserts
     *
     * @return array
     */

    public function map()
    {

        return [
            'userid' => FIELD_TYPE_INCREMENTS,
            'username' => FIELD_TYPE_STRING,
            'email' => FIELD_TYPE_STRING,
            'password' => FIELD_TYPE_STRING
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