<?php

namespace App\Services;

class SdltCalculatorService
{
    /**
     * @return array{
     *     scenario: string,
     *     total_sdlt: float,
     *     effective_rate: float,
     *     breakdown: array<int, array{
     *         from: float,
     *         to: float|null,
     *         taxable_amount: float,
     *         base_rate: float,
     *         surcharge_rate: float,
     *         total_rate: float,
     *         tax_paid: float
     *     }>
     * }
     */
    public function calculate(float $price, bool $isFirstTimeBuyer, bool $isAdditionalProperty): array
    {
        $priceInPence = (int) round($price * 100);
        $useFirstTimeBuyerRates = $isFirstTimeBuyer && $price <= config('sdlt.first_time_buyer.cap');

        $bands = $useFirstTimeBuyerRates
            ? config('sdlt.first_time_buyer.bands')
            : config('sdlt.standard_bands');

        $surchargeRate = $isAdditionalProperty ? (float) config('sdlt.additional_property_surcharge') : 0.0;

        $previousBandUpperInPence = 0;
        $totalTaxInPence = 0;
        $breakdown = [];

        foreach ($bands as $band) {
            $bandUpperInPence = is_null($band['up_to']) ? null : (int) round($band['up_to'] * 100);
            $baseRate = (float) $band['rate'];
            $combinedRate = $baseRate + $surchargeRate;

            $taxableAmountInPence = $this->calculateTaxableAmount(
                $priceInPence,
                $previousBandUpperInPence,
                $bandUpperInPence
            );

            if ($taxableAmountInPence <= 0) {
                if (! is_null($bandUpperInPence)) {
                    $previousBandUpperInPence = $bandUpperInPence;
                }

                continue;
            }

            $bandTaxInPence = (int) round($taxableAmountInPence * $combinedRate);
            $totalTaxInPence += $bandTaxInPence;

            $breakdown[] = [
                'from' => $previousBandUpperInPence / 100,
                'to' => is_null($bandUpperInPence) ? null : $bandUpperInPence / 100,
                'taxable_amount' => $taxableAmountInPence / 100,
                'base_rate' => $baseRate,
                'surcharge_rate' => $surchargeRate,
                'total_rate' => $combinedRate,
                'tax_paid' => $bandTaxInPence / 100,
            ];

            if (! is_null($bandUpperInPence)) {
                $previousBandUpperInPence = $bandUpperInPence;
            }
        }

        $totalSdlt = $totalTaxInPence / 100;

        return [
            'scenario' => $this->determineScenario($useFirstTimeBuyerRates, $isAdditionalProperty),
            'total_sdlt' => $totalSdlt,
            'effective_rate' => $price > 0 ? ($totalSdlt / $price) * 100 : 0.0,
            'breakdown' => $breakdown,
        ];
    }

    private function calculateTaxableAmount(int $priceInPence, int $fromInPence, ?int $toInPence): int
    {
        if (is_null($toInPence)) {
            return max(0, $priceInPence - $fromInPence);
        }

        if ($priceInPence <= $fromInPence) {
            return 0;
        }

        return max(0, min($priceInPence, $toInPence) - $fromInPence);
    }

    private function determineScenario(bool $useFirstTimeBuyerRates, bool $isAdditionalProperty): string
    {
        if ($isAdditionalProperty) {
            return 'Additional property rates';
        }

        if ($useFirstTimeBuyerRates) {
            return 'First-time buyer relief rates';
        }

        return 'Standard residential rates';
    }
}
