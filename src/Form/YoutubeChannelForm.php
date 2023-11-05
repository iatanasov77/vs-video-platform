<?php namespace App\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;

use App\Entity\YoutubeChannel;

class YoutubeChannelForm extends AbstractForm
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $builder
            ->add( 'title', TextType::class, [
                'label'                 => 'vs_vvp.form.youtube_channel.title',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
            
            ->add( 'channelId', TextType::class, [
                'label'                 => 'vs_vvp.form.youtube_channel.channel_id',
                'translation_domain'    => 'VanzVideoPlayer',
            ])
        ;
        
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefaults([
                'data_class'        => YoutubeChannel::class,
                'csrf_protection'   => false,
            ])
        ;
    }
    
    public function getName()
    {
        return 'vs_vvp.youtube_channel';
    }
}