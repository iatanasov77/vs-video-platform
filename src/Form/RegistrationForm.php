<?php namespace App\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;

use Vankosoft\UsersBundle\Form\UserFormType;
use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;
use Vankosoft\UsersBundle\Form\Traits\UserInfoFormTrait;

/*
 * Form Inheritance:
 * https://stackoverflow.com/questions/22414166/inherit-form-or-add-type-to-each-form
 */
class RegistrationForm extends UserFormType
{
    use UserInfoFormTrait;
    
    /** @var string */
    private $pricingPlanClass;
    
    /** @var RepositoryInterface */
    private $pricingPlanRepository;
    
    public function __construct(
        string $dataClass,
        RepositoryInterface $localesRepository,
        RequestStack $requestStack,
        string $applicationClass,
        string $userRolesClass,
        AuthorizationCheckerInterface $auth,
        array $requiredFields,
        string $pricingPlanClass,
        RepositoryInterface $pricingPlanRepository
    ) {
        parent::__construct(
            $dataClass,
            $localesRepository,
            $requestStack,
            $applicationClass,
            $userRolesClass,
            $auth,
            $requiredFields
        );
        
        $this->pricingPlanClass         = $pricingPlanClass;
        $this->pricingPlanRepository    = $pricingPlanRepository;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        $this->buildUserInfoForm( $builder, $options );
        
        $builder->remove( 'enabled' );
        $builder->remove( 'verified' );
        
        $builder->remove( 'roles_options' );
        $builder->remove( 'username' );
        
        $builder->remove( 'applications' );
        $builder->remove( 'allowedRoles' );
        
        $builder->remove( 'btnSave' );
        
        $builder
            ->setMethod( 'POST' )
            
            ->add( 'pricingPlan', ChoiceType::class, [
                'label'                 => 'vs_vvp.form.register.pricing_plan_select',
                'placeholder'           => 'vs_vvp.form.register.pricing_plan_select_placeholder',
                'translation_domain'    => 'VanzVideoPlayer',
                'choices'               => $this->pricingPlanRepository->findAllForForm([
                    'test-plans',
                ]),
                'required'              => true,
                'mapped'                => false,
            ])
            
            ->add( 'agreeTerms', CheckboxType::class, [
                'label'                 => 'vs_users.form.registration.agreement_text',
                'translation_domain'    => 'VSUsersBundle',
                'mapped'                => false,
            ])
            
            ->add( 'btnRgister', SubmitType::class, [
                'label' => 'vs_users.form.registration.register',
                'translation_domain' => 'VSUsersBundle'
            ])
            
            ////////////////////////////////////////////////
            // Additional Fields
            ////////////////////////////////////////////////
            ->add( 'birthday', BirthdayType::class, [
                'mapped'                => false,
                'label'                 => 'vs_vvp.form.register.date_of_birth',
                'placeholder'           => [
                    'year'  => 'vs_vvp.form.register.year',
                    'month' => 'vs_vvp.form.register.month',
                    'day'   => 'vs_vvp.form.register.day',
                ],
                'translation_domain'    => 'VanzVideoPlayer',
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {   
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefaults([
                'csrf_protection'       => true,
                
                'profilePictureMapped'  => false,
                'titleMapped'           => false,
                'firstNameMapped'       => false,
                'lastNameMapped'        => false,
                'designationMapped'     => false,
            ])
            ->setDefined([
                'users',
            ])
            ->setAllowedTypes( 'users', UserInterface::class )
        ;
    }

    public function getName()
    {
        return 'vs_users.registration';
    }
}
