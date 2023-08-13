<?php

namespace App\Shared\Type;

use Webmozart\Assert\Assert;

class PaginationData
{
    protected int $limit;

    /**
     * 0-based offset. First $offset elements should be skipped.
     */
    protected int $offset;

    /**
     * @param int|null $limit
     * @param int|null $offset
     */
    public function __construct(?int $limit = null, ?int $offset = null)
    {
        if ($limit !== null) {
            $this->setLimit($limit);
        }

        if ($offset !== null) {
            $this->setOffset($offset);
        }
    }

    /**
     * @param int $pageSize
     * @param int $pageNumber
     *
     * @return self
     */
    public static function createFromPageData(int $pageSize, int $pageNumber): self
    {
        $limit  = $pageSize;
        $offset = ($pageNumber - 1) * $pageSize;

        return new self($limit, $offset);
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit): void
    {
        Assert::greaterThan($limit, 0);

        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $offset
     */
    public function setOffset(int $offset): void
    {
        Assert::greaterThanEq($offset, 0);

        $this->offset = $offset;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }
}
