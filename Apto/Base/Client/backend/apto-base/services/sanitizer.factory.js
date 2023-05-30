const FactoryInject = [];
const Factory = function() {
    const IDENTIFIER_CTABLE = [
        // substitute special chars
        { search: '©', replace: 'c' },
        { search: 'ß', replace: 'ss' },
        { search: 'ψ', replace: 'ps' },
        { search: '[Þþ]', replace: 'th' },
        { search: 'ξ', replace: '3' },
        { search: 'θ', replace: '8' },
        { search: '[àáâãåăαά]', replace: 'a' },
        { search: '[æä]', replace: 'ae' },
        { search: 'β', replace: 'b' },
        { search: 'ç', replace: 'c' },
        { search: '[ðδ]', replace: 'd' },
        { search: '[èéêëεέ]', replace: 'e' },
        { search: 'φ', replace: 'f' },
        { search: '[ğγ]', replace: 'g' },
        { search: '[ηή]', replace: 'h' },
        { search: '[ìíîïıίϊΐ]', replace: 'i' },
        { search: 'κ', replace: 'k' },
        { search: 'λ', replace: 'l' },
        { search: 'μ', replace: 'm' },
        { search: '[ñν]', replace: 'n' },
        { search: '[òóôõőøο]', replace: 'o' },
        { search: 'ö', replace: 'oe' },
        { search: 'π', replace: 'p' },
        { search: 'ρ', replace: 'r' },
        { search: '[șσς]', replace: 's' },
        { search: '[țτ]', replace: 't' },
        { search: '[ùúûű]', replace: 'u' },
        { search: 'ü', replace: 'ue' },
        { search: '[ωώ]', replace: 'w' },
        { search: 'χ', replace: 'x' },
        { search: '[ýÿυύΰϋ]', replace: 'y' },
        { search: 'ζ', replace: 'z' },

        // remove remaining special chars
        { search: '[^a-z0-9\\. \\t\\-_]', replace: '' },

        // convert _, tabs and spaces to -
        { search: '[\\t _]', replace: '-' },

        // reduce multiple -
        { search: '-+', replace: '-' },

        // remove - and _ at beginning/end
        { search: '^[-_]', replace: '' },
        { search: '[-_]$', replace: '' }
    ];

    function sanitizeIdentifier(identifier) {
        let sanitized = identifier.toLowerCase();

        IDENTIFIER_CTABLE.forEach((rule) => {
            sanitized = sanitized.replace(new RegExp(rule.search, 'gu'), rule.replace);
        });

        return sanitized;
    }

    return {
        sanitizeIdentifier: sanitizeIdentifier
    };
};

Factory.$inject = FactoryInject;

export default ['SanitizerFactory', Factory];
