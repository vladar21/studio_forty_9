<?php
namespace App\InventoryBundle\Message;

class StockOutMessage
{
    private string $sku;
    private string $branch;

    public function __construct(string $sku, string $branch)
    {
        $this->sku = $sku;
        $this->branch = $branch;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function getBranch(): string
    {
        return $this->branch;
    }
}