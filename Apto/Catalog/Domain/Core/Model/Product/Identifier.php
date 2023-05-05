<?php
namespace Apto\Catalog\Domain\Core\Model\Product;

class Identifier
{
    const IDENTIFIER_CTABLE = [
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
        '[\t _]' => '-',

        // reduce multiple -
        '-+' => '-',

        // remove - and _ at beginning/end
        '^[-_]' => '',
        '[-_]$' => ''
    ];

    /**
     * @var string
     */
    private $value;

    /**
     * Identifier constructor.
     * @param string $value
     * @throws \Exception
     */
    public function __construct(string $value)
    {
        $sanitized = $this->sanitize($value);

        if(strlen($sanitized) <= 0) {
            throw new \Exception('Identifier cant be an empty string!');
        }

        $this->value = $sanitized;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param Identifier $identifier
     * @return bool
     */
    public function equals(Identifier $identifier)
    {
        return $this->getValue() === $identifier->getValue();
    }

    /**
     * @param string $value
     * @return string
     */
    private function sanitize(string $value): string
    {
        $sanitized = strtolower($value);

        foreach (self::IDENTIFIER_CTABLE as $rule => $subst) {
            $sanitized = preg_replace('/' . $rule . '/u', $subst, $sanitized);
        }

        return $sanitized;
    }
}