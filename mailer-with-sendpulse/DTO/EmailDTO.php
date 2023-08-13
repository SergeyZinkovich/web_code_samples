<?php

declare(strict_types=1);

namespace App\Service\Email;


class EmailDTO
{
    protected $emailFrom = '';

    /**
     * @param string $emailFrom
     */
    public function __construct(string $emailFrom)
    {
        $this->emailFrom = $emailFrom;
    }

    /**
     * @return mixed|string
     */
    public function getEmailFrom()
    {
        return $this->emailFrom;
    }
}
