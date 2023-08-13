<?php

declare(strict_types=1);

namespace App\Service\Order\Notifier;

use App\Service\Common\Sms\SmsTemplating;
use App\Service\Email\EmailDTO;
use Creonit\SendPulseMailer\Header\SendPulseVariableHeader;
use Creonit\SmsBundle\Message\SmsMessage;
use Creonit\StorageBundle\Storage\Storage;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;

abstract class AbstractNotifier
{
    protected MessageBusInterface $messageBus;
    protected MailerInterface $mailer;
    protected NormalizerInterface $normalizer;
    protected SmsTemplating $smsTemplating;
    protected EmailDTO $emailService;
    protected Storage $storage;
    protected ?string $adminEmail = null;

    /**
     * @param MessageBusInterface $messageBus
     * @param MailerInterface $mailer
     * @param NormalizerInterface $normalizer
     * @param SmsTemplating $smsTemplating
     * @param EmailDTO $emailService
     * @param Storage $storage
     */
    public function __construct(
        MessageBusInterface $messageBus,
        MailerInterface     $mailer,
        NormalizerInterface $normalizer,
        SmsTemplating       $smsTemplating,
        EmailDTO            $emailService,
        Storage             $storage
    ) {
        $this->messageBus = $messageBus;
        $this->mailer = $mailer;
        $this->normalizer = $normalizer;
        $this->smsTemplating = $smsTemplating;
        $this->emailService = $emailService;

        $this->adminEmail = $storage->get('defaultAdminEmail')['email'];
    }

    /**
     * @param array $context
     *
     * @return array
     *
     * @throws ExceptionInterface
     */
    protected function normalizeContext(array $context): array
    {
        foreach ($context as &$value) {
            if (is_array($value)) {
                $value = $this->normalizeContext($value);
            } else {
                $value = $this->normalizer->normalize($value, 'notify');
            }
        }

        return $context;
    }

    /**
     * @param string $event
     * @param array $context
     *
     * @return Email
     */
    protected function buildEmailMessage(string $event, array $context = []): Email
    {
        $message = new Email();
        $message->getHeaders()->addTextHeader('X-SendPulse-Event', $event);

        foreach ($context as $key => $value) {
            $message->getHeaders()->add(new SendPulseVariableHeader($key, $value));
        }

        return $message;
    }

    /**
     * @param string $template
     * @param array $context
     *
     * @return SmsMessage
     *
     * @throws LoaderError
     * @throws SyntaxError
     */
    protected function buildSmsMessage(string $template, array $context = []): SmsMessage
    {
        $content = $this->smsTemplating->render($template, $context);
        $message = new SmsMessage();
        $message->setContent($content);

        return $message;
    }

    /**
     * @param array|string|null $to
     * @param string $event
     * @param array $context
     *
     * @throws TransportExceptionInterface
     * @throws ExceptionInterface
     */
    public function sendEmail($to, string $event, array $context = [])
    {
        if ($to === null) {
            return;
        }

        $message = $this->buildEmailMessage($event, $this->normalizeContext($context));

        if (!is_array($to)) {
            $to = [$to];
        }

        $message
            ->from($this->emailService->getEmailFrom())
            ->text('Body')
            ->to(...$to);

        $this->mailer->send($message);
    }

    /**
     * @param array|string $to
     * @param string $template
     * @param array $context
     *
     * @throws ExceptionInterface
     * @throws LoaderError
     * @throws SyntaxError
     */
    public function sendSms($to, string $template, array $context = [])
    {
        $message = $this->buildSmsMessage($template, $this->normalizeContext($context));

        if (!is_array($to)) {
            $to = [$to];
        }

        $message->setTo(...$to);
        $this->messageBus->dispatch($message);
    }
}
