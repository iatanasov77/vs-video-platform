<?php

return [
    Symfony\Bundle\MonologBundle\MonologBundle::class => ['all' => true],
    Symfony\Bundle\WebProfilerBundle\WebProfilerBundle::class => ['dev' => true, 'staging' => true, 'test' => true],
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Symfony\Bundle\DebugBundle\DebugBundle::class => ['dev' => true, 'staging' => true, 'test' => true],
	Symfony\WebpackEncoreBundle\WebpackEncoreBundle::class => ['all' => true],
	
	Twig\Extra\TwigExtraBundle\TwigExtraBundle::class => ['all' => true],
    BabDev\PagerfantaBundle\BabDevPagerfantaBundle::class => ['all' => true],
	
	SymfonyCasts\Bundle\VerifyEmail\SymfonyCastsVerifyEmailBundle::class => ['all' => true],
    SymfonyCasts\Bundle\ResetPassword\SymfonyCastsResetPasswordBundle::class => ['all' => true],
	
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],
    Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle::class => ['all' => true],
    
    Knp\Bundle\MenuBundle\KnpMenuBundle::class => ['all' => true],
    Knp\Bundle\PaginatorBundle\KnpPaginatorBundle::class => ['all' => true],
	Knp\Bundle\TimeBundle\KnpTimeBundle::class => ['all' => true],
	Artgris\Bundle\FileManagerBundle\ArtgrisFileManagerBundle::class => ['all' => true],
	Liip\ImagineBundle\LiipImagineBundle::class => ['all' => true],
	
    Sylius\Bundle\ResourceBundle\SyliusResourceBundle::class => ['all' => true],
    Sylius\Bundle\ThemeBundle\SyliusThemeBundle::class => ['all' => true],
    
	Vankosoft\AgentBundle\VSAgentBundle::class => ['all' => true],
    Vankosoft\ApplicationInstalatorBundle\VSApplicationInstalatorBundle::class => ['all' => true],
    Vankosoft\ApplicationBundle\VSApplicationBundle::class => ['all' => true],
    Vankosoft\CmsBundle\VSCmsBundle::class => ['all' => true],
    Vankosoft\UsersBundle\VSUsersBundle::class => ['all' => true],
	
	FOS\JsRoutingBundle\FOSJsRoutingBundle::class => ['all' => true],
    Bazinga\Bundle\JsTranslationBundle\BazingaJsTranslationBundle::class => ['all' => true],
	Hackzilla\Bundle\PasswordGeneratorBundle\HackzillaPasswordGeneratorBundle::class => ['all' => true],
    
    Oneup\FlysystemBundle\OneupFlysystemBundle::class => ['all' => true],
    
    Payum\Bundle\PayumBundle\PayumBundle::class => ['all' => true],
    Vankosoft\UsersSubscriptionsBundle\VSUsersSubscriptionsBundle::class => ['all' => true],
    Vankosoft\PaymentBundle\VSPaymentBundle::class => ['all' => true],
    Vankosoft\CatalogBundle\VSCatalogBundle::class => ['all' => true],
    
    ApiPlatform\Symfony\Bundle\ApiPlatformBundle::class => ['all' => true],
    Vankosoft\ApiBundle\VSApiBundle::class => ['all' => true],
    Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle::class => ['all' => true],
    Gesdinet\JWTRefreshTokenBundle\GesdinetJWTRefreshTokenBundle::class => ['all' => true],
    Nelmio\CorsBundle\NelmioCorsBundle::class => ['all' => true],
    Symfony\Bundle\MercureBundle\MercureBundle::class => ['all' => true],
    
    Dubture\FFmpegBundle\DubtureFFmpegBundle::class => ['all' => true],
];
