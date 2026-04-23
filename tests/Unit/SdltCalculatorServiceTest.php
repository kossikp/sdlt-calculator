<?php

namespace Tests\Unit;

use App\Services\SdltCalculatorService;
use Tests\TestCase;

class SdltCalculatorServiceTest extends TestCase
{
    public function test_it_calculates_standard_rates(): void
    {
        $service = app(SdltCalculatorService::class);

        $result = $service->calculate(300000, false, false);

        $this->assertSame('Standard residential rates', $result['scenario']);
        $this->assertEquals(5000.0, $result['total_sdlt']);
        $this->assertEqualsWithDelta(1.6667, $result['effective_rate'], 0.0001);
    }

    public function test_it_calculates_first_time_buyer_relief_within_cap(): void
    {
        $service = app(SdltCalculatorService::class);

        $result = $service->calculate(400000, true, false);

        $this->assertSame('First-time buyer relief rates', $result['scenario']);
        $this->assertEquals(5000.0, $result['total_sdlt']);
        $this->assertEqualsWithDelta(1.25, $result['effective_rate'], 0.0001);
    }

    public function test_it_falls_back_to_standard_rates_when_first_time_buyer_cap_exceeded(): void
    {
        $service = app(SdltCalculatorService::class);

        $result = $service->calculate(600000, true, false);

        $this->assertSame('Standard residential rates', $result['scenario']);
        $this->assertEquals(20000.0, $result['total_sdlt']);
    }

    public function test_it_calculates_additional_property_surcharge(): void
    {
        $service = app(SdltCalculatorService::class);

        $result = $service->calculate(300000, false, true);

        $this->assertSame('Additional property rates', $result['scenario']);
        $this->assertEquals(20000.0, $result['total_sdlt']);
    }

    public function test_it_handles_exact_threshold_values(): void
    {
        $service = app(SdltCalculatorService::class);

        $result = $service->calculate(250000, false, false);

        $this->assertEquals(2500.0, $result['total_sdlt']);
        $this->assertCount(2, $result['breakdown']);
    }
}
