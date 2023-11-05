<?php namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

/**
 * @Gedmo\TranslationEntity(class="App\Entity\Application\Translation")
 * @ORM\Entity
 * @ORM\Table(name="VVP_HelpCenterQuestions")
 */
class HelpCenterQuestion implements ResourceInterface, TranslatableInterface
{
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
     * @ORM\Column(name="question", type="string", length=255, nullable=false)
     */
    private $question;
    
    /**
     * @Gedmo\Translatable
     * @ORM\Column(name="answer", type="text", nullable=false)
     */
    private $answer;
    
    /**
     * @Gedmo\Locale
     * Used locale to override Translation listener`s locale
     * this is not a mapped field of entity metadata, just a simple property
     */
    private $locale;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getQuestion()
    {
        return $this->question;
    }
    
    public function setQuestion($question)
    {
        $this->question   = $question;
        
        return $this;
    }
    
    public function getAnswer()
    {
        return $this->answer;
    }
    
    public function setAnswer($answer)
    {
        $this->answer  = $answer;
        
        return $this;
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