<?php

declare(strict_types=1);

namespace App\Service\Common\Templating;

use App\Service\Common\Templating\AbstractTemplating;

class SmsTemplating extends AbstractTemplating
{
    /**
     * @return string
     */
    protected function getStorageItem(): string
    {
        return 'sms_templates';
    }
}
