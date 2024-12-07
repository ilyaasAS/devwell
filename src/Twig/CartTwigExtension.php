<?php

// src/Twig/CartTwigExtension.php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use App\Service\CartService;

class CartTwigExtension extends AbstractExtension implements GlobalsInterface
{
    private $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function getGlobals(): array
    {
        return [
            'totalItems' => $this->cartService->getTotalItems(),
        ];
    }
}
