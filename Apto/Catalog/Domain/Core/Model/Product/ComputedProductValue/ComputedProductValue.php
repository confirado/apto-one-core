<?php
namespace Apto\Catalog\Domain\Core\Model\Product\ComputedProductValue;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Apto\Base\Domain\Core\Model\AptoEntity;
use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\FileSystem\MediaFileSystemConnector;
use Apto\Base\Domain\Core\Model\InvalidUuidException;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Product;
use Apto\Catalog\Domain\Core\Service\Formula\FunctionParser;

class ComputedProductValue extends AptoEntity
{
    const NAME_CTABLE = [
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
     * @var string
     */
    private $name;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var string
     */
    private $formula;

    /**
     * @var Collection
     */
    private $aliases;

    /**
     * @param string $filename
     * @return string
     */
    public static function sanitizeName(string $filename): string
    {
        $sanitized = strtolower($filename);

        foreach (self::NAME_CTABLE as $rule => $subst) {
            $sanitized = preg_replace('/' . $rule . '/u', $subst, $sanitized);
        }

        return $sanitized;
    }

    /**
     * @param AptoUuid $id
     * @param string $name
     * @param Product $product
     * @throws ComputedProductValueNameNotValidException
     */
    public function __construct(AptoUuid $id, string $name, Product $product)
    {
        parent::__construct($id);

        $this->setName($name);
        $this->product = $product;
        $this->formula = "";
        $this->aliases = new ArrayCollection();

        $this->publish(
            new ComputedProductValueAdded(
                $this->getId()
            )
        );
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     * @throws ComputedProductValueNameNotValidException
     */
    public function setName(string $name): ComputedProductValue
    {
        $this->name = ComputedProductValue::sanitizeName($name);
        $this->assertValidName();
        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     * @return ComputedProductValue
     */
    public function setProduct(Product $product): ComputedProductValue
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormula(): string
    {
        return $this->formula;
    }

    /**
     * @param string $formula
     * @return ComputedProductValue
     */
    public function setFormula(string $formula): ComputedProductValue
    {
        $this->formula = $formula;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getAliases(): Collection
    {
        return $this->aliases;
    }

    /**
     * @param Collection $aliases
     * @return ComputedProductValue
     */
    public function setAliases(Collection $aliases): ComputedProductValue
    {
        $this->aliases = $aliases;
        return $this;
    }

    /**
     * @param string $name
     * @return AptoUuid|null
     */
    public function getAliasId(string $name): ?AptoUuid
    {
        /** @var Alias $alias */
        foreach ($this->aliases as $alias) {
            if ($alias->getName() === $name) {
                return $alias->getId();
            }
        }

        return null;
    }

    /**
     * @param string $sectionId
     * @param string|null $elementId
     * @param string $name
     * @param string $property
     * @param bool $isCP
     * @return $this
     * @throws AliasNotUniqueException
     * @throws InvalidAliasException
     */
    public function addAlias(string $sectionId, ?string $elementId, string $name, string $property = "", bool $isCP = false ): ComputedProductValue
    {
        $this->checkForDuplicateAlias($name);

        $this->aliases->add(
            new Alias(
                new AptoUuid(),
                $sectionId,
                $elementId,
                $this,
                $name,
                $property,
                $isCP
            )
        );
        return $this;
    }

    /**
     * @param string $id
     * @return ComputedProductValue
     */
    public function removeAlias(string $id): ComputedProductValue
    {
        foreach ($this->aliases as $alias) {
            if ($alias->getId()->getId() === $id) {
                $this->aliases->removeElement($alias);
            }
        }
        return $this;
    }

    /**
     * @param State $state
     * @param array $calculatedValues
     * @param MediaFileSystemConnector|null $mediaFileSystem
     * @return string
     * @throws InvalidUuidException
     */
    public function getValue(State $state, array $calculatedValues = [], ?MediaFileSystemConnector $mediaFileSystem = null): string
    {
        if (!$this->formula) {
            return '0';
        }

        $filledFormula = $this->fillNestedValues(array_merge($calculatedValues, [
            '_anzahl_' => $state->getParameter(State::QUANTITY)
        ]));
        $variables = $this->getAliasValues($state, $this->product);

        $aliases = [];
        /** @var Alias $alias */
        foreach ($this->getAliases() as $alias) {
            $aliases[$alias->getName()] = [
                'sectionId' => $alias->getSectionId(),
                'elementId' => $alias->getElementId(),
                'property' => $alias->getProperty(),
                'isCustomProperty' => $alias->isCustomProperty()
            ];
        }

        try {
            return math_eval(
                FunctionParser::parse($filledFormula, $variables, $mediaFileSystem, $aliases, $state),
                $variables
            );
        } catch (\Exception $exception) {
            return '0';
        }
    }

    /**
     * @param State $state
     * @param Product $product
     * @return array
     * @throws InvalidUuidException
     */
    private function getAliasValues(State $state, Product $product): array
    {
        $values = [];
        /* @var Alias $alias */
        foreach ($this->aliases as $alias) {
            $values[$alias->getName()] = $alias->getAliasValue($state, $product);
        }
        return $values;
    }

    /**
     * @param $calculatedValues
     * @return string
     */
    private function fillNestedValues($calculatedValues): string
    {
        $pattern = '/\\{.*?\\}/';
        $variables = [];
        $filledFormula = $this->formula;
        preg_match_all($pattern, $this->formula, $variables);

        foreach ($variables[0] as $variable) {
            $variableName = str_replace('{', '', str_replace('}', '', $variable));

            if (array_key_exists($variableName, $calculatedValues)) {
                $filledFormula = str_replace($variable, $calculatedValues[$variableName], $filledFormula );
            }
        }
        return $filledFormula;
    }

    /**
     * @param AptoUuid $computedProductValueId
     * @param Collection $entityMapping
     * @return ComputedProductValue
     * @throws ComputedProductValueNameNotValidException
     * @throws InvalidAliasException
     */
    public function copy(AptoUuid $computedProductValueId, Collection &$entityMapping): ComputedProductValue
    {
        $computedProductValue =  new ComputedProductValue(
            $computedProductValueId,
            $this->getName(),
            $entityMapping->get($this->product->getId()->getId())
        );

        $computedProductValue->setFormula($this->getFormula());
        $computedProductValue->setAliases($this->copyAliases($computedProductValue, $entityMapping));

        // add new ComputedProductValue to entityMapping
        $entityMapping->set(
            $this->getId()->getId(),
            $computedProductValue
        );

        return $computedProductValue;
    }

    /**
     * @param ComputedProductValue $computedProductValue
     * @param Collection $entityMapping
     * @return ArrayCollection
     * @throws InvalidAliasException
     */
    private function copyAliases(ComputedProductValue $computedProductValue, Collection $entityMapping): ArrayCollection
    {
        $aliases = new ArrayCollection();
        /** @var Alias $alias */
        foreach ($this->getAliases() as $alias) {
            $newAlias = new Alias(
                new AptoUuid(),
                $entityMapping->get($alias->getSectionId())->getId()->getId(),
                $entityMapping->get($alias->getElementId())->getId()->getId(),
                $computedProductValue,
                $alias->getName(),
                $alias->getProperty(),
                $alias->isCustomProperty()
            );
            $aliases->add($newAlias);
        }
        return $aliases;
    }

    /**
     * @return void
     * @throws ComputedProductValueNameNotValidException
     */
    private function assertValidName()
    {
        if ($this->name === '') {
            throw new ComputedProductValueNameNotValidException('The name must not be empty.');
        }
    }

    /**
     * @param string $name
     * @throws AliasNotUniqueException
     */
    private function checkForDuplicateAlias(string $name)
    {
        /* @var Alias $alias */
        foreach ($this->aliases as $alias) {
            if ($alias->getName() === $name) {
                throw new AliasNotUniqueException('Alias name must be unique per formula');
            }
        }
    }
}
