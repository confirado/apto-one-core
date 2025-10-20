<?php
namespace Apto\Plugins\PdfGenerator\Domain\Core\Model\OfferHtml;

use Apto\Base\Domain\Core\Model\AptoAggregate;
use Apto\Base\Domain\Core\Model\AptoUuid;

class OfferHtml extends AptoAggregate
{

    /**
     * @phpstan-ignore-next-line
     * @var string
     */
    private $number;

    /**
     * @phpstan-ignore-next-line
     * @var string
     */
    private $header;

    /**
     * @phpstan-ignore-next-line
     * @var string
     */
    private $footer;

    /**
     * @phpstan-ignore-next-line
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
