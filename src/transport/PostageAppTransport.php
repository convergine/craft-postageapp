<?php
namespace convergine\postageapp\transport;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\HttpTransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractApiTransport;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class PostageAppTransport extends AbstractApiTransport {
    private const HOST = 'api.postageapp.com';
    private const VERSION = 'v.1.1';

    private string $key;

    public function __construct(string $key, HttpClientInterface $client = null, EventDispatcherInterface $dispatcher = null, LoggerInterface $logger = null) {
        $this->key = $key;
        parent::__construct($client, $dispatcher, $logger);
    }

    public function __toString(): string {
        return sprintf('postageapp+api://%s', $this->getEndpoint());
    }

    protected function doSendApi(SentMessage $sentMessage, Email $email, Envelope $envelope): ResponseInterface {
        $response = $this->client->request('POST', 'https://'.$this->getEndpoint().'/send_message', [
            'headers' => [
                'Accept' => 'application/json',
                'User-Agent' => 'PostageApp PHP ' . phpversion(),
            ],
            'json' => [
                'api_key' => $this->key,
                'arguments' => $this->getPayload($email, $envelope)
            ]
        ]);

        try {
            $statusCode = $response->getStatusCode();
            $result = $response->toArray(false);
        } catch (DecodingExceptionInterface $e) {
            throw new HttpTransportException('Unable to send an email: '.$response->getContent(false).sprintf(' (code %d).', $statusCode), $response);
        } catch (TransportExceptionInterface $e) {
            throw new HttpTransportException('Could not reach the remote PostageApp server.', $response, 0, $e);
        }

        if(200 !== $statusCode) {
            throw new HttpTransportException('Unable to send an email: '.$result['response']['message'].sprintf(' (%d).', $result['response']['status']), $response);
        }

        $sentMessage->setMessageId($result['data']['message']['id']);

        return $response;
    }

    private function getPayload(Email $email, Envelope $envelope): array {
        return [
            'recipients' => array_merge(
                $this->stringifyAddresses($this->getRecipients($email, $envelope)),
                $this->stringifyAddresses($email->getCc()),
                $this->stringifyAddresses($email->getBcc())
            ),
            'headers' => [
                'subject' => $email->getSubject(),
                'from' => $envelope->getSender()->toString(),
                'reply-to' => implode(',', $this->stringifyAddresses($email->getReplyTo()))
            ],
            'content' => [
                'text/plain' => $email->getTextBody(),
                'text/html' => $email->getHtmlBody()
            ],
            'attachments' => $this->getAttachments($email)
        ];
    }

    private function getAttachments(Email $email): array {
        $attachments = [];
        foreach ($email->getAttachments() as $attachment) {
            $headers = $attachment->getPreparedHeaders();
            $filename = $headers->getHeaderParameter('Content-Disposition', 'filename');

            $attachments[$filename] = [
                'content_type' => $headers->get('Content-Type')->getBody(),
                'content' => $attachment->bodyToString()
            ];
        }

        return $attachments;
    }

    private function getEndpoint(): ?string {
        return self::HOST.'/'.self::VERSION;
    }
}
