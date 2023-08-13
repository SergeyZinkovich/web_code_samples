<?php

namespace App\Model;

class OrderEmailItem
{
    protected int $id;
    protected string $title;
    protected int $quantity;
    protected int $total_price;
    protected int $price;
    protected int $article;
    protected string $slug;
    protected string $img;

    /**
     * @param string $title
     * @param string $slug
     * @param string $img
     * @param int $id
     * @param int $quantity
     * @param int $total_price
     * @param int $price
     * @param int $article
     */
    public function __construct(
        string $title,
        string $slug,
        string $img,
        int $id,
        int $quantity,
        int $total_price,
        int $price,
        int $article
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->quantity = $quantity;
        $this->total_price = $total_price;
        $this->price = $price;
        $this->article = $article;
        $this->slug = $slug;
        $this->img = $img;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     *
     * @return self
     */
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotalPrice(): int
    {
        return $this->total_price;
    }

    /**
     * @param int $total_price
     *
     * @return self
     */
    public function setTotalPrice(int $total_price): self
    {
        $this->total_price = $total_price;

        return $this;
    }

    /**
     * @return string
     */
    public function getImg(): string
    {
        return $this->img;
    }

    /**
     * @param string $img
     *
     * @return self
     */
    public function setImg(string $img): self
    {
        $this->img = $img;

        return $this;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @param int $price
     *
     * @return self
     */
    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return int
     */
    public function getArticle(): int
    {
        return $this->article;
    }

    /**
     * @param int $article
     *
     * @return self
     */
    public function setArticle(int $article): self
    {
        $this->article = $article;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return self
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

}
