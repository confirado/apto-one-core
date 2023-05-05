<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Controller\MessageResponse;

use DateTimeImmutable;
use JsonSerializable;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

abstract class MessageResponse implements JsonSerializable
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var float
     */
    protected $duration;

    /**
     * @var array
     */
    protected $payload;

    /**
     * @var DateTimeImmutable
     */
    protected $dateTime;

    /**
     * Create a new message response
     * @param string $name
     * @param string $message
     * @param float $duration
     * @param array $payload
     */
    public function __construct(string $name, string $message, float $duration, array $payload = [])
    {
        $this->name = $name;
        $this->message = $message;
        $this->duration = $duration;
        $this->payload = $payload;
        $this->dateTime = new DateTimeImmutable();
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return array_merge(
            $this->payload,
            [
                'message' => [
                    'messageName' => $this->name,
                    'message' => $this->message,
                    'date' => $this->dateTime->format('d.m.Y H:i:s'),
                    'duration' => round($this->duration, 4)
                ]
            ]
        );
    }

    /**
     * Return message as json encoded http response
     * @param SerializerInterface $serializer
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public function getJsonResponse(SerializerInterface $serializer, int $status = 200, array $headers = ['Content-Type' => 'application/json']): Response
    {
        return new Response(
            $serializer->serialize(
                $this->jsonSerialize(),
                'json'
            ),
            $status,
            $headers
        );
    }

}