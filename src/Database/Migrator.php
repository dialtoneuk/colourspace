<?php
namespace Colourspace\Database;

use Colourspace\Container;
use Colourspace\Database\Interfaces\TableInterface;
use Colourspace\Framework\Util\Constructor;
use Colourspace\Framework\Util\Debug;
use Illuminate\Database\Schema\Blueprint;

class Migrator
{

    protected $constructor;
    protected $connection;

    /**
     * Migrator constructor.
     * @param bool $auto_initialize
     * @throws \Error
     */

    public function __construct( $auto_initialize=true )
    {

        if( Container::has("application") == false )
            throw new \Error("Application has not been initialized");

        $this->connection = Container::get('application')->connection->connection;

        $this->constructor = new Constructor( TABLES_ROOT, TABLES_NAMESPACE );

        if( $auto_initialize )
            $this->initialize();
    }

    /**
     * @throws \Error
     */

    public function initialize()
    {

        if( file_exists( COLOURSPACE_ROOT . TABLES_ROOT ) == false )
            throw new \Error("Tables do not exist");

        if( is_file( COLOURSPACE_ROOT . TABLES_ROOT  ) )
            throw new \Error("Must be the locaiton of a folder");

        $this->constructor->createAll();
    }

    /**
     * @throws \Error
     */

    public function process()
    {

        foreach ( $this->constructor->getAll() as $name=>$class )
        {

            if( defined("CMD") )
                echo( " --> Using class: $name \n");

            if( DEBUG_ENABLED )
                Debug::message('Creating table for ' . $name );

            if( $class instanceof TableInterface == false )
                throw new \Error("Incorrect class");

            /** @var TableInterface $class */
            $table_name = strtolower( $class->name() );

            if( empty( $class->map() ) )
                throw new \Error("No migrator map in class");

            if( $this->tableExists( $table_name ) )
                continue;

            $this->create( $table_name, $class->map() );

            if( DEBUG_ENABLED )
                Debug::message('Finished creating table for ' . $name );
        }
    }

    /**
     * @param string $table_name
     * @param array $table_map
     */

    private function create( string $table_name, array $table_map )
    {

        $this->connection->getSchemaBuilder()->create( $table_name, function( Blueprint $table ) use ( $table_map ){

            foreach ( $table_map as $coloum=>$type )
            {

                if( defined("CMD") )
                    echo( " ---> Create column: $coloum with type $type \n");

                switch ( $type )
                {

                    case FIELD_TYPE_STRING:
                        $table->string( $coloum );
                        break;
                    case FIELD_TYPE_INT:
                        $table->integer( $coloum );
                        break;
                    case FIELD_TYPE_TIMESTAMP:
                        $table->timestamp( $coloum );
                        break;
                    case FIELD_TYPE_INCREMENTS:
                        $table->increments( $coloum );
                        break;
                    case FIELD_TYPE_DECIMAL:
                        $table->decimal( $coloum );
                        break;
                    case FIELD_TYPE_IPADDRESS:
                        $table->ipAddress( $coloum );
                        break;
                    case FIELD_TYPE_JSON:
                        $table->longText( $coloum );
                        break;
                }
            }
        });
    }

    /**
     * @param $table_name
     * @return bool
     */

    private function tableExists( $table_name )
    {

        if( defined("CMD") )
            echo( " ---> Table exists: $table_name \n");

        if( $this->connection->getSchemaBuilder()->hasTable( $table_name ) )
            return true;

        return false;
    }
}