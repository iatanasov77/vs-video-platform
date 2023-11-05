<?php namespace App\Component\VideoPlayer\Domain;

class Video
{
    /**
     * @var string
     */
    private $videoId;
    
    /**
     * @var string
     */
    private $videoSlug;
    
    /**
     * @var string
     */
    private $videoUrl;
    
    /**
     * @var string
     */
    private $title;
    
    /**
     * @var string|null
     */
    private $description;
    
    /**
     * @var string|null
     */
    private $author;
    
    /**
     * @var string
     */
    private $thumbnail;
    
    public function __construct( string $videoId, string $title, string $description = null, string $videoUrl = null, string $videoSlug = null )
    {
        $this->videoId      = $videoId;
        $this->title        = $title;
        $this->description  = $description;
        $this->videoUrl     = $videoUrl;
        $this->videoSlug    = $videoSlug;
    }
    
    public function videoId(): string
    {
        return $this->videoId;
    }
    
    public function videoSlug(): string
    {
        return $this->videoSlug;
    }
    
    public function videoUrl(): string
    {
        return $this->videoUrl;
    }
    
    public function title(): string
    {
        return $this->title;
    }
    
    public function description(): ?string
    {
        return $this->description;
    }
    
    public function createdBy( string $author ): Video
    {
        $this->author   = $author;
        
        return $this;
    }
    
    /**
     * @return null|string
     */
    public function author(): ?string
    {
        return $this->author;
    }
    
    public function thumbnail(): ?string
    {
        return $this->thumbnail;
    }
    
    /**
     * @param string $thumbnail
     * @return Video
     */
    public function withThumbnail( string $thumbnail ): Video
    {
        $this->thumbnail    = $thumbnail;
        
        return $this;
    }
    
    
}