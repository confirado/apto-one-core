<?php
namespace Apto\Plugins\RequestForm\Domain\Core\Model\OfferHtml;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoUuid;

class OfferHtml extends AptoAggregate
{

    /**
     * @var string
     */
    private $number;

    /**
     * @var string
     */
    private $header;

    /**
     * @var string
     */
    private $footer;

    /**
     * @var string
     */
    private $body;

    /**
     * @param AptoUuid $id
     * @param string $number
     * @param string $header
     * @param string $footer
     * @param string $body
     */
    public function __construct(AptoUuid $id, string $number, string $header, string $footer, string $body)
    {
        parent::__construct($id);
        $this->publish(
            new OfferHtmlAdded(
                $this->getId()
            )
        );
        $this->number = $number;
        $this->header = $header;
        $this->footer = $footer;
        $this->body = $body;
    }
}
