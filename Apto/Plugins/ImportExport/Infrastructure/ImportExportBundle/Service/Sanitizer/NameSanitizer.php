<?php

namespace Apto\Plugins\ImportExport\Infrastructure\ImportExportBundle\Service\Sanitizer;

class NameSanitizer
{
    const FILENAME_CTABLE = [
        // substitute special chars
        '©' => 'c',
        'ß' => 'ss',
        'ψ' => 'ps',
        '[Þþ]' => 'th',
        'ξ' => '3',
        'θ' => '8',
        '[àáâãåăαά]' => 'a',
        '[æä]' => 'ae',
        'β' => 'b',
        'ç' => 'c',
        '[ðδ]' => 'd',
        '[èéêëεέ]' => 'e',
        'φ' => 'f',
        '[ğγ]' => 'g',
        '[ηή]' => 'h',
        '[ìíîïıίϊΐ]' => 'i',
        'κ' => 'k',
        'λ' => 'l',
        'μ' => 'm',
        '[ñν]' => 'n',
        '[òóôõőøο]' => 'o',
        'ö' => 'oe',
        'π' => 'p',
        'ρ' => 'r',
        '[șσς]' => 's',
        '[țτ]' => 't',
        '[ùúûű]' => 'u',
        'ü' => 'ue',
        '[ωώ]' => 'w',
        'χ' => 'x',
        '[ýÿυύΰϋ]' => 'y',
        'ζ' => 'z',

        // remove remaining special chars
        '[^a-z0-9\. \t\-_]' => '',

        // convert tabs to spaces
        '[\t]' => ' ',
    ];

    /**
     * @param string $filename
     * @return String
     */
    public static function sanitizeName(string $filename): string
    {
        $sanitized = $filename;

        foreach (self::FILENAME_CTABLE as $rule => $subst) {
            $sanitized = preg_replace('/' . $rule . '/iu', $subst, $sanitized);
        }

        return $sanitized;
    }
}