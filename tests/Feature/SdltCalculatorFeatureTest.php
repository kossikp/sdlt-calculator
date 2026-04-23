<?php

namespace Tests\Feature;

use Tests\TestCase;

class SdltCalculatorFeatureTest extends TestCase
{
    public function test_home_page_renders_calculator_form(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Stamp Duty Land Tax (SDLT) Calculator')
            ->assertSee('Calculate SDLT');
    }

    public function test_valid_submission_renders_sdlt_result(): void
    {
        $response = $this->post('/calculate', [
            'price' => 300000,
            'first_time_buyer' => '0',
            'additional_property' => '0',
        ]);

        $response->assertOk()
            ->assertSee('Calculation result')
            ->assertSee('GBP 5,000.00')
            ->assertSee('Tax breakdown');
    }

    public function test_invalid_combination_shows_validation_error_message(): void
    {
        $response = $this->from('/')->post('/calculate', [
            'price' => 250000,
            'first_time_buyer' => '1',
            'additional_property' => '1',
        ]);

        $response->assertRedirect('/')
            ->assertSessionHasErrors('first_time_buyer');
    }

    public function test_invalid_price_shows_validation_error_message(): void
    {
        $response = $this->from('/')->post('/calculate', [
            'price' => -1,
            'first_time_buyer' => '0',
            'additional_property' => '0',
        ]);

        $response->assertRedirect('/')
            ->assertSessionHasErrors('price');
    }
}
