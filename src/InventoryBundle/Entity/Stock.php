<?php
namespace App\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Represents the stock level of a product at a specific branch.
 *
 * @ORM\Entity()
 * @ORM\Table(name="stocks")
 */
class Stock
{
    /**
     * Unique identifier for the stock item.
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * Stock Keeping Unit, uniquely identifies a product.
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="SKU is required.")
     */
    private string $sku;

    /**
     * Identifier for the branch where the stock is located.
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Branch ID is required.")
     */
    private string $branch;

    /**
     * Quantity of stock available.
     *
     * @ORM\Column(type="decimal", scale=2)
     * @Assert\NotNull(message="Stock quantity cannot be null.")
     * @Assert\Type(
     *     type="numeric",
     *     message="The stock value must be a numeric value."
     * )
     */
    private $stock;

    // Getters and setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function setSku(string $sku): self
    {
        $this->sku = $sku;

        return $this;
    }

    public function getBranch(): string
    {
        return $this->branch;
    }

    public function setBranch(string $branch): self
    {
        $this->branch = $branch;

        return $this;
    }

    public function getStock(): float
    {
        return $this->stock;
    }

    public function setStock(float $stock): self
    {
        $this->stock = $stock;
        return $this;
    }


}