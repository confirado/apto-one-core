<?php

namespace Apto\Base\Domain\Core\Model\Customer;

class Gender implements \JsonSerializable
{
    /**
     * Valid values for gender
     */
    const UNKNOWN = 0;
    const MALE = 1;
    const FEMALE = 2;

    /**
     * @var array
     */
    protected static $validValues = [
        self::UNKNOWN,
        self::MALE,
        self::FEMALE
    ];

    /**
     * @var int
     */
    protected $gender;

    /**
     * @param string $gender
     * @return Gender
     * @throws InvalidGenderException
     */
    public static function createFromString(string $gender): Gender
    {
        switch ($gender) {
            case '':
            case ' ':
                return new self(self::UNKNOWN);

            case 'm':
                return new self(self::MALE);

            case 'f':
            case 'w':
                return new self(self::FEMALE);
        }

        throw new InvalidGenderException('The given value \'' . $gender . '\' is not a valid gender.');
    }

    /**
     * Gender constructor.
     * @param int $gender
     * @throws InvalidGenderException
     */
    public function __construct(int $gender)
    {
        if (!in_array($gender, self::$validValues)) {
            throw new InvalidGenderException('The given value \'' . $gender . '\' is not a valid gender.');
        }

        $this->gender = $gender;
    }

    /**
     * @return int
     */
    public function getGender(): int
    {
        return $this->gender;
    }

    /**
     * @param Gender $gender
     * @return bool
     */
    public function equals(Gender $gender): bool
    {
        return $gender->getGender() == $this->getGender();
    }

    /**
     * @return int
     */
    public function jsonSerialize(): int
    {
        return $this->gender;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        switch ($this->gender) {
            case self::UNKNOWN:
                return '';

            case self::MALE:
                return 'm';

            case self::FEMALE:
                return 'f';
        }

        return '';
    }


}