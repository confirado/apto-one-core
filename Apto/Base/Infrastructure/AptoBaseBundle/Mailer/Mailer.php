<?php

namespace Apto\Base\Infrastructure\AptoBaseBundle\Mailer;

use Twig\Loader\FilesystemLoader;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

use Apto\Base\Application\Core\Service\RequestStore;
use Apto\Base\Application\Core\Service\TemplateMailerInterface;
use Apto\Base\Domain\Core\Service\AptoParameterInterface;

class Mailer implements TemplateMailerInterface
{
    /**
     * @var FilesystemLoader
     */
    private $twigFilesystemLoader;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var RequestStore
     */
    private $requestStore;

    /**
     * @var AptoParameterInterface
     */
    private $aptoParameter;

    /**
     * @param FilesystemLoader $twigFilesystemLoader
     * @param MailerInterface $mailer
     * @param RequestStore $requestStore
     * @param AptoParameterInterface $aptoParameter
     */
    public function __construct(
        FilesystemLoader $twigFilesystemLoader,
        MailerInterface $mailer,
        RequestStore $requestStore,
        AptoParameterInterface $aptoParameter
    ) {
        $this->twigFilesystemLoader = $twigFilesystemLoader;
        $this->mailer = $mailer;
        $this->requestStore = $requestStore;
        $this->aptoParameter = $aptoParameter;
    }

    /**
     * @param array $payload
     * @return void
     * @throws TransportExceptionInterface
     */
    public function send(array $payload)
    {
        $email = $this->getNewMail();
        $email
            ->from(
                new Address(
                    $payload['from']['email'],
                    $payload['from']['name'] ?: ''
                )
            )
            ->to(
                new Address(
                    $payload['to']['email'],
                    $payload['to']['name'] ?: ''
                )
            )
            ->subject(
                $payload['subject']
            )
            ->htmlTemplate(
                $payload['template']
            )
            ->context(
                $payload['context']
            )
        ;

        $this->cc($email, $payload);
        $this->bcc($email, $payload);
        $this->addAttachments($email, $payload);

        $this->mailer->send($email);
    }

    /**
     * @param string $template
     * @return bool
     */
    public function templateExists(string $template): bool
    {
        return $this->twigFilesystemLoader->exists($template);
    }

    /**
     * @return TemplatedEmail
     */
    private function getNewMail(): TemplatedEmail
    {
        $email = new TemplatedEmail();

        if (!$this->aptoParameter->has('mailer_transports')) {
            return $email;
        }

        // set mail transport by domain
        $host = $this->requestStore->getHttpHost();
        $mailerTransports = $this->aptoParameter->get('mailer_transports');

        if (array_key_exists($host, $mailerTransports)) {
            $email->getHeaders()->addTextHeader('X-Transport', str_replace('-', '_', $host));
        }

        return $email;
    }

    /**
     * @param TemplatedEmail $email
     * @param array $payload
     * @return void
     */
    private function addAttachments(TemplatedEmail $email, array $payload)
    {
        if (array_key_exists('attachments', $payload)) {
            foreach ($payload['attachments'] as $attachment) {
                if (array_key_exists('path', $attachment)) {
                    $email->attachFromPath($attachment['path'], $attachment['name'] ?: null, $attachment['contentType'] ?: null);
                } else {
                    $email->attach($attachment['content'], $attachment['name'] ?: null, $attachment['contentType'] ?: null);
                }
            }
        }
    }

    /**
     * @param TemplatedEmail $email
     * @param array $payload
     * @return void
     */
    private function cc(TemplatedEmail $email, array $payload)
    {
        if (array_key_exists('cc', $payload)) {
            $addresses = [];

            foreach ($payload['cc'] as $bcc) {
                $addresses[] = new Address(
                    $bcc['email'],
                    $bcc['name'] ?: ''
                );
            }

            if (count($addresses) > 0) {
                $email->cc(...$addresses);
            }
        }
    }

    /**
     * @param TemplatedEmail $email
     * @param array $payload
     * @return void
     */
    private function bcc(TemplatedEmail $email, array $payload)
    {
        if (array_key_exists('bcc', $payload)) {
            $addresses = [];

            foreach ($payload['bcc'] as $bcc) {
                $addresses[] = new Address(
                    $bcc['email'],
                    $bcc['name'] ?: ''
                );
            }

            if (count($addresses) > 0) {
                $email->bcc(...$addresses);
            }
        }
    }
}
