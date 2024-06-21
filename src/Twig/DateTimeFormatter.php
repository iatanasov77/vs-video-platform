<?php namespace App\Twig;

use Symfony\Contracts\Translation\TranslatorInterface;

final class DateTimeFormatter
{
    /**
     * @internal
     */
    public function __construct(private TranslatorInterface $translator)
    {
    }

    /**
     * @author Fabien Potencier <fabien@symfony.com>
     *
     * @source https://github.com/symfony/symfony/blob/ad72245261792c6b5d2db821fcbd141b11095215/src/Symfony/Component/Console/Helper/Helper.php#L97
     */
    public function formatDuration(float $seconds, string $locale = null): string
    {
        static $timeFormats = [
            [0, 'duration.none'],
            [1, 'duration.second'],
            [2, 'duration.second', 1],
            [60, 'duration.minute'],
            [120, 'duration.minute', 60],
            [3600, 'duration.hour'],
            [7200, 'duration.hour', 3600],
            [86400, 'duration.day'],
            [172800, 'duration.day', 86400],
        ];

        foreach ($timeFormats as $index => $format) {
            if ($seconds >= $format[0]) {
                if ((isset($timeFormats[$index + 1]) && $seconds < $timeFormats[$index + 1][0])
                    || $index === \count($timeFormats) - 1
                ) {
                    if (2 === \count($format)) {
                        return $this->translator->trans($format[1], ['%count%' => 1], 'time', $locale);
                    }

                    $time   = $this->convert( $seconds );
                    return $this->translator->trans(
                        $format[1],
                        ['%count%' => $time],
                        'time',
                        $locale
                    );
                }
            }
        }

        return $this->translator->trans('duration.none', [], 'time', $locale);
    }
    
    private function convert( float $seconds ): string
    {
        $secs = $seconds % 60;
        $hrs = $seconds / 60;
        $mins = $hrs % 60;
        
        $hrs = $hrs / 60;
        if ( (int)$hrs ) {
            return (int)$hrs . "." . (int)$mins;
        }
        
        return (int)$mins . "." . (int)$secs;
    }
}
