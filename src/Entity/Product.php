<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Table(name="products")
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @UniqueEntity(
 *     fields={"article"},
 *     message="entity.field.unique"
 * )
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
	 * @Assert\NotBlank(message="entity.field.not_blank")
     */
    private $name;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $tradePrice;

    /**
     * @ORM\Column(type="integer")
     */
    private $packs;

    /**
     * @ORM\Column(type="integer")
     */
    private $inPack;

    /**
     * @ORM\Column(type="integer")
     */
    private $outPack;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
	 * @Assert\NotBlank(message="entity.field.not_blank")
     */
    private $article;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ProductCategory", inversedBy="products")
     */
    private $categories;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default"=true})
     */
    private $isActive;

    /**
     * @ORM\Column(type="text", length=65535, nullable=true)
     */
    private $description;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getTradePrice()
    {
        return $this->tradePrice;
    }

    public function setTradePrice($tradePrice): self
    {
        $this->tradePrice = $tradePrice;

        return $this;
    }

    public function getPacks(): ?int
    {
        return $this->packs;
    }

    public function setPacks(int $packs): self
    {
        $this->packs = $packs;

        return $this;
    }

    public function getInPack(): ?int
    {
        return $this->inPack;
    }

    public function setInPack(int $inPack): self
    {
        $this->inPack = $inPack;

        return $this;
    }

    public function getOutPack(): ?int
    {
        return $this->outPack;
    }

    public function setOutPack(int $outPack): self
    {
        $this->outPack = $outPack;

        return $this;
    }

    public function getArticle(): ?string
    {
        return $this->article;
    }

    public function setArticle(string $article): self
    {
        $this->article = $article;

        return $this;
    }

    /**
     * @return Collection|ProductCategory[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(ProductCategory $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(ProductCategory $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

}
