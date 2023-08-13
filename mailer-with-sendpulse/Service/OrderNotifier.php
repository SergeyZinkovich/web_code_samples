<?php

declare(strict_types=1);

namespace App\Service\Order\Notifier;


use App\Model\Order;
use App\Model\OrderEmailItem;
use App\Model\User;
use App\Service\Common\Notifier\AbstractNotifier;
use App\Service\Common\SendPulse\SendPulseEvents;
use App\Service\Common\Sms\SmsTemplating;
use App\Service\Email\EmailDTO;
use App\Service\Order\Method\Delivery\DeliveryProvider;
use App\Service\Order\Method\Exception\CodeOrderMethodNotFoundException;
use App\Service\Order\Method\Payment\PaymentProvider;
use Creonit\StorageBundle\Storage\Storage;
use Propel\Runtime\Exception\PropelException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use App\Service\Product\Repository\ProductRepository;

class OrderNotifier extends AbstractNotifier
{
    private DeliveryProvider $deliveryProvider;
    private PaymentProvider $paymentProvider;
    private ContainerInterface $container;
    private ProductRepository $productRepository;

    /**
     * @param MessageBusInterface $messageBus
     * @param MailerInterface $mailer
     * @param NormalizerInterface $normalizer
     * @param SmsTemplating $smsTemplating
     * @param EmailDTO $emailService
     * @param Storage $storage
     * @param DeliveryProvider $deliveryProvider
     * @param PaymentProvider $paymentProvider
     * @param ContainerInterface $container
     * @param ProductRepository $productRepository
     */
    public function __construct(
        MessageBusInterface $messageBus,
        MailerInterface     $mailer,
        NormalizerInterface $normalizer,
        SmsTemplating       $smsTemplating,
        EmailDTO            $emailService,
        Storage             $storage,
        DeliveryProvider    $deliveryProvider,
        PaymentProvider     $paymentProvider,
        ContainerInterface  $container,
        ProductRepository   $productRepository
    ) {
        parent::__construct($messageBus, $mailer, $normalizer, $smsTemplating, $emailService, $storage);

        $this->deliveryProvider = $deliveryProvider;
        $this->paymentProvider = $paymentProvider;
        $this->container = $container;
        $this->storage = $storage;
        $this->productRepository = $productRepository;
    }

    /**
     * @param Order $order
     *
     * @throws CodeOrderMethodNotFoundException
     * @throws ExceptionInterface
     * @throws PropelException
     * @throws TransportExceptionInterface
     */
    public function sendNewOrderUserEmailNotification(Order $order): void
    {
        $user = $order->getUser();

        $params = [
            'email' => $order->getUser()->getEmail(),
            'phone' => $order->getUser()->getPhone(),
            'delivery_address' => $order->getOrderDeliveryData()->getAddress(),
            'delivery_method' => $this->deliveryProvider->loadOrderMethodByCode($order->getOrderDeliveryData()->getMethodCode())->getTitle(),
            'payment_method' => $this->paymentProvider->loadOrderMethodByCode($order->getOrderPaymentData()->getMethodCode())->getTitle(),
            'items' => $this->getOrderItemsEmail($order->getOrderItems()->getData()),
            'delivery_price' => $order->getOrderDeliveryData()->getPrice(),
            'order_total_price' => $order->getTotalPrice(),
            'order_created_date' => $order->getCreatedAt('d.m.Y'),
            'order_created_time' => $order->getCreatedAt('H:i:s'),
            'base_url' => $this->getBaseUri(),
            'header' => $this->storage->get('header')['positioning'],
            'full_name' => $user->getFullName(),
            'order_number' => $order->getId(),
            'items_count' => count($order->getOrderItems()->getData()),
            'general_phone' => $this->storage->get('generalContacts')['phone1'],
            'general_contacts_phone1' => $this->storage->get('generalContacts')['phone1'],
            'general_contacts_phone2' => $this->storage->get('generalContacts')['phone2'],
            'general_contacts_email' => $this->storage->get('generalContacts')['email'],
            'city' => $this->storage->get('generalAddress')['city'],
            'street' => $this->storage->get('generalAddress')['street'],
            'roof' => $this->storage->get('generalAddress')['roof'],
            'location1' => $this->storage->get('generalAddress')['location1'],
            'workTime1' => $this->storage->get('generalAddress')['workTime1'],
            'workTime2' => $this->storage->get('generalAddress')['workTime2'],
            'location2' => $this->storage->get('generalAddress')['location2'],
            'workTime3' => $this->storage->get('generalAddress')['workTime3'],
            'workTime4' => $this->storage->get('generalAddress')['workTime4'],
            'social' => $this->storage->get('generalSocial'),
        ];

        $this->sendEmail($user->getEmail(), SendPulseEvents::EVENT_NEW_ORDER_USER, $params);
    }

    /**
     * @return string
     */
    public function getBaseUri(): string
    {
        $scheme = $this->container->get('router')->getContext()->getScheme();
        $host = $this->container->get('router')->getContext()->getHost();
        $baseUrl = $this->container->get('router')->getContext()->getBaseUrl();
        return $scheme . '://' . $host . '/' . $baseUrl;
    }

    /**
     * @param array $orderItems
     *
     * @return array
     */
    public function getOrderItemsEmail(array $orderItems): array
    {
        $orderEmailItems = [];
        foreach ($orderItems as $orderItem) {

            $scheme = $this->container->get('router')->getContext()->getScheme();
            $host = $this->container->get('router')->getContext()->getHost();

            $product = $this->productRepository->getProductById($orderItem->getProductId());

            $imageFile = $product->getGallery()->getGalleryItems()->getData()[0]->getImage()->getFile();
            $img = $scheme . '://' . $host . $imageFile->getPath() . '/' . $imageFile->getName();

            $item = new OrderEmailItem(
                $orderItem->getTitle(),
                $product->getSlug(),
                $img,
                $orderItem->getProductId(),
                $orderItem->getQuantity(),
                $orderItem->getTotalPrice(),
                $orderItem->getPrice(),
                $product->getArticle(),
            );

            $orderEmailItems[] = $item;
        }

        return $orderEmailItems;
    }
}
