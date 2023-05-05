<?php
namespace Apto\Base\Application\Core\Service\ShopConnector;

class ConnectorConfig
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $token;

    /**
     * @var array
     */
    private $sessionCookies;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $shopId;

    /**
     * Create a new ConnectorConfig from array data
     * @param array $data
     * @param array $sessionCookies
     * @return ConnectorConfig
     */
    public static function fromArray(array $data, array $sessionCookies = []): self
    {
        return new self(
            $data['connectorUrl'],
            $data['connectorToken'],
            $data['currency'],
            $data['shopId'],
            $sessionCookies
        );
    }

    /**
     * ConnectorConfig constructor.
     * @param string $url
     * @param string $token
     * @param string $currency
     * @param string $shopId
     * @param array $sessionCookies
     */
    public function __construct(string $url, string $token, string $currency, string $shopId, array $sessionCookies)
    {
        $this->url = $url;
        $this->token = $token;
        $this->sessionCookies = $sessionCookies;
        $this->currency = $currency;
        $this->shopId = $shopId;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return array
     */
    public function getSessionCookies(): array
    {
        return $this->sessionCookies;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getShopId(): string
    {
        return $this->shopId;
    }

    /**
     * @return string
     */
    public function getCookieHeaderString(): string
    {
        $cookieHeaderString = '';
        $divider = '; ';
        $isFirst = true;

        foreach ($this->getSessionCookies() as $cookieName => $cookieValue) {
            if (false === $isFirst) {
                $cookieHeaderString .= $divider;
            }

            $cookieHeaderString .= $cookieName . '=' . $cookieValue;
            $isFirst = false;
        }
        return $cookieHeaderString;
    }
}