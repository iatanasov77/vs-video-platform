<?php namespace App\Twig\Extension;

use App\Twig\DateTimeFormatter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * @internal
 */
final class TimeExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'vs_duration',
                [DateTimeFormatter::class, 'formatDuration'],
                ['is_safe' => ['html']]
            ),
        ];
    }
}
