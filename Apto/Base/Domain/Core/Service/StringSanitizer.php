<?php

namespace Apto\Base\Domain\Core\Service;

class StringSanitizer
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

        // convert _, tabs and spaces to -
        '[\t ]' => '-',

        // reduce multiple -
        '-+' => '-',

        // remove - and _ at beginning/end
        '^[-_]' => '',
        '[-_]$' => ''
    ];

    /**
     * @param string $filename
     * @return String
     */
    public function sanitizeFilename(string $filename): string
    {
        $sanitized = strtolower($filename);

        foreach (self::FILENAME_CTABLE as $rule => $subst) {
            $sanitized = preg_replace('/' . $rule . '/u', $subst, $sanitized);
        }

        return $sanitized;
    }
}