<?php
/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 06/07/2018
 * Time: 01:22
 */

namespace Colourspace\Framework;

use Colourspace\Database\Tables\Blogs;
use Colourspace\Framework\Util\Colours;
use Colourspace\Framework\Util\Format;

class Blog
{

    /**
     * @var Blogs
     */

    protected $blogs;

    /**
     * Blog constructor.
     * @throws \Error
     */

    public function __construct()
    {

        $this->blogs = new Blogs();
    }

    /**
     * @param $blogid
     * @return bool
     */

    public function has( $blogid )
    {

        return( $this->blogs->has( $blogid ) );
    }

    /**
     * @param $blogid
     * @return \Illuminate\Support\Collection
     */

    public function get( $blogid )
    {

        return( $this->blogs->get( $blogid ) );
    }

    /**
     * @param $blogid
     * @return mixed
     */

    public function getContent( $blogid )
    {

        return( json_decode( Format::decodeLargeText( $this->get( $blogid )->content ), true ) );
    }

    /**
     * @param $blogid
     * @param array $values
     */

    public function editContent( $blogid, array $values )
    {

        $content = $this->getContent( $blogid );

        foreach( $values as $key=>$value )
        {

            if( isset( $content[ $key ] ) )
                $content[ $key ] = $values;
        }

        $this->blogs->update(["blogid" => $blogid], ["content" => Format::largeText( json_encode( $content ) ) ] );
    }

    /**
     * @param int $userid
     * @param string $title
     * @param string $body
     * @param string|null $colour
     * @return int
     * @throws \Error
     */

    public function create( int $userid, string $title, string $body, string $colour=null )
    {

        return( $this->blogs->insert([
            "userid" => $userid,
            "content" => $this->getContentArray( $title, $body, $colour ),
            "creation" => Format::timestamp()
        ]));
    }

    /**
     * @param $title
     * @param $body
     * @param null $colour
     * @return array
     * @throws \Error
     */

    private function getContentArray( $title, $body, $colour=null )
    {

        if( $colour == null )
            $colour = Colours::generate();

        return([
            "title" => $title,
            "body" => $body,
            "colour" => $colour
        ]);
    }
}