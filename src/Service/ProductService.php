<?php

namespace App\Service;

//use App\Repository\ProductRepository;

class ProductService
{
    private $productRepository;

//    public function __construct(ProductRepository $productRepository)
//    {
//        $this->productRepository = $productRepository;
//    }
//
//    public function getProductById(int $productId): ?array
//    {
//        return $this->productRepository->find($productId); // Assuming your product repository has a find method
//    }

    public function getProductById(int $productId): ?array
    {
        // Пример имитации данных о продукте
        $products = [
            1 => ['id' => 1, 'name' => 'Iphone', 'price' => 100001],
            2 => ['id' => 2, 'name' => 'Наушники', 'price' => 20],
            3 => ['id' => 3, 'name' => 'Чехол', 'price' => 10],
        ];

        return $products[$productId] ?? null;
    }
}
