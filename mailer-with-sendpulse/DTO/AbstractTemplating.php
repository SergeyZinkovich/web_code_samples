<?php

declare(strict_types=1);

namespace App\Service\Common\Templating;

use Creonit\StorageBundle\Storage\Storage;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;

abstract class AbstractTemplating
{
    protected array $templates = [];
    protected Storage $storage;
    protected Environment $environment;

    /**
     * @param Storage $storage
     * @param Environment $environment
     */
    public function __construct(Storage $storage, Environment $environment)
    {
        $this->environment = $environment;
        $this->storage = $storage;
        $this->setTemplateTitle($this->getStorageItem());
    }

    /**
     * @return string
     */
    abstract protected function getStorageItem(): string;

    /**
     * @param string $title
     */
    public function setTemplateTitle(string $title): void
    {
        $this->templates = (array) $this->storage->get($title) ?? [];
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function getTemplate(string $name)
    {
        return $this->templates[$name] ?? null;
    }

    /**
     * @param string $templateName
     * @param array $context
     *
     * @return string
     *
     * @throws LoaderError
     * @throws SyntaxError
     */
    public function render(string $templateName, array $context): string
    {
        if (!$template = $this->getTemplate($templateName)) {
            return '';
        }

        return $this->environment->createTemplate($template)->render($context);
    }
}
