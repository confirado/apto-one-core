<?php
namespace Apto\Catalog\Domain\Core\Model\Product;

class IdentifierNullable
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
     * @var string|null
     */
    private $value;

    /**
     * IdentifierNullable constructor.
     * @param string|null $value
     */
    public function __construct(string $value = null)
    {
        $this->value = $this->sanitize($value);
    }

    /**
     * @return string|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param IdentifierNullable $identifier
     * @return bool
     */
    public function equals(IdentifierNullable $identifier)
    {
        return $this->getValue() === $identifier->getValue();
    }

    /**
     * @param string|null $value
     * @return string|null
     */
    private function sanitize(string $value = null)
    {
        if (null === $value) {
            return null;
        }

        $sanitized = strtolower($value);

        foreach (self::IDENTIFIER_CTABLE as $rule => $subst) {
            $sanitized = preg_replace('/' . $rule . '/u', $subst, $sanitized);
        }

        if ('' === $sanitized) {
            return null;
        }

        return $sanitized;
    }
}