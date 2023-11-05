<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

/**
 * @Gedmo\TranslationEntity(class="App\Entity\Application\Translation")
 * @ORM\Entity
 * @ORM\Table(name="VVP_Sliders")
 */
class Slider implements ResourceInterface, TranslatableInterface
{
    use ToggleableTrait;    // About enabled field - $enabled (public)
    use TranslatableTrait;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @Gedmo\Translatable
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;
    
    /**
     * @Gedmo\Translatable
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    private $description;
    
    /**
     * @ORM\OneToOne(targetEntity=SliderPhoto::class, mappedBy="owner", cascade={"persist", "remove"})
     */
    private $photo;
    
    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;
    
    /**
     * @var bool
     *
     * @ORM\Column(name="public", type="boolean", options={"default":"1"})
     */
    protected $enabled = true;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function setTitle($title)
    {
        $this->title   = $title;
        
        return $this;
    }
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public function setDescription($description)
    {
        $this->description  = $description;
        
        return $this;
    }
    
    public function getPhoto()
    {
        return $this->photo;
    }
    
    public function setPhoto($photo)
    {
        $this->photo = $photo;
        
        return $this;
    }
    
    public function isPublic(): bool
    {
        return $this->enabled;
    }
    
    public function isPublished(): bool
    {
        return $this->enabled;
    }
    
    public function getLocale()
    {
        return $this->currentLocale;
    }
    
    public function getTranslatableLocale(): ?string
    {
        return $this->locale;
    }
    
    public function setTranslatableLocale($locale): self
    {
        $this->locale = $locale;
        
        return $this;
    }
    
    protected function createTranslation(): TranslationInterface
    {
        
    }
}