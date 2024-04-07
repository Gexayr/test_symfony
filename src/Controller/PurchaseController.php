<?php
// src/Controller/PurchaseController.php

namespace App\Controller;

use App\Service\ProductService;
use App\Service\TaxService;
use App\Service\CouponService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;


class PurchaseController extends AbstractController
{
    private $productService;
    private $taxService;
    private $couponService;

    public function __construct(ProductService $productService, TaxService $taxService, CouponService $couponService)
    {
        $this->productService = $productService;
        $this->taxService = $taxService;
        $this->couponService = $couponService;
    }

    /**
     * @Route("/purchase", methods={"POST"})
     */
    public function purchase(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Проверяем обязательные поля "product", "taxNumber" и "paymentProcessor"
            if (!isset($data['product']) || !isset($data['taxNumber']) || !isset($data['paymentProcessor'])) {
                throw new \Exception('Required fields "product", "taxNumber", and "paymentProcessor" are missing');
            }

            $productId = $data['product'];
            $taxNumber = $data['taxNumber'];
            $paymentProcessorType = $data['paymentProcessor'];
            $couponCode = $data['couponCode'] ?? null; // Получаем couponCode или null, если не указан

            // Получаем информацию о продукте
            $product = $this->productService->getProductById($productId);
            if (!$product) {
                throw new \Exception('Product not found');
            }

            $price = $product['price'];

            // Получаем ставку налога по номеру
            $taxRate = $this->taxService->getTaxRateByNumber($taxNumber);

            // Инициализируем переменную для скидки
            $discountAmount = 0;

            // Применяем скидку к итоговой цене, если указан код купона
            if ($couponCode) {
                $coupon = $this->couponService->getCouponByCode($couponCode);
                if (!$coupon) {
                    throw new \Exception('Coupon not found');
                }

                if ($coupon['type'] === 'percentage') {
                    $discountAmount = $price * $coupon['value']; // Применяем процентную скидку
                } elseif ($coupon['type'] === 'fixed') {
                    $discountAmount = $coupon['value']; // Применяем фиксированную сумму скидки
                }
            }

            // Вычисляем итоговую цену с учетом налогов и скидки
            $finalPrice = ($price + ($price * $taxRate)) - $discountAmount;


            // Создаем экземпляр платежного процессора в зависимости от типа
            $paymentProcessor = null;
            if ($paymentProcessorType === 'paypal') {
                $paymentProcessor = new PaypalPaymentProcessor();
                $paymentResult = $paymentProcessor->pay($finalPrice);

            } elseif ($paymentProcessorType === 'stripe') {
                $paymentProcessor = new StripePaymentProcessor();
                $paymentResult = $paymentProcessor->processPayment($finalPrice);
            } else {
                throw new \Exception('Invalid payment processor type');
            }

            if (!$paymentResult) {
                throw new \Exception('Payment processing failed');
            }

            // Возвращаем успешный ответ с HTTP кодом 200
            return new JsonResponse(['message' => 'Purchase successful', 'finalPrice' => $finalPrice], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            // Возвращаем ошибку с HTTP кодом 400 и описанием ошибки
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}

