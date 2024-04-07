<?php

namespace App\Service;

//use App\Repository\TaxRepository;

class TaxService
{
    private $taxRepository;

//    public function __construct(TaxRepository $taxRepository)
//    {
//        $this->taxRepository = $taxRepository;
//    }
//
//    public function getTaxRateByNumber(string $taxNumber): float
//    {
//        return $this->taxRepository->findByTaxNumber($taxNumber)->getRate(); // Example implementation
//    }

    public function getTaxRateByNumber(string $taxNumber): float
    {
        // Пример имитации данных о ставке налога
        $taxRates = [
            'IT12345678900' => 0.20, // Налоговая ставка для номера 'IT12345678900'
            'DE123456789' => 0.10, // Налоговая ставка для номера 'IT12345678900'
        ];

        return $taxRates[$taxNumber] ?? 0.00;
    }
}
