<?php

namespace Apto\Catalog\Domain\Core\Model\Configuration;

use Apto\Base\Domain\Core\Model\AptoUuid;
use Apto\Base\Domain\Core\Model\Email;
use Apto\Catalog\Domain\Core\Model\Configuration\State\State;
use Apto\Catalog\Domain\Core\Model\Product\Product;

class GuestConfiguration extends Configuration
{
    /**
     * @var Email
     */
    protected $email;

    /**
     * @var string
     */
    protected $name;

    /**
     * GuestConfiguration constructor.
     * @param AptoUuid $id
     * @param Product $product
     * @param State $state
     * @param Email $email
     * @param string $name
     */
    public function __construct(AptoUuid $id, Product $product, State $state, Email $email, string $name = '')
    {
        parent::__construct($id, $product, $state);

        $this->email = $email;
        $this->name = $name;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}