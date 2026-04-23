<?php

namespace App\Http\Controllers;

use App\Services\SdltCalculatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SdltCalculatorController extends Controller
{
    public function __construct(private SdltCalculatorService $sdltCalculatorService) {}

    public function index()
    {
        return view('sdlt-calculator');
    }

    public function calculate(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'price' => ['required', 'numeric', 'gt:0', 'max:999999999'],
                'first_time_buyer' => ['nullable', 'boolean'],
                'additional_property' => ['nullable', 'boolean'],
            ],
            [
                'price.required' => 'Enter the property purchase price.',
                'price.numeric' => 'The purchase price must be a number.',
                'price.gt' => 'The purchase price must be greater than zero.',
                'price.max' => 'The purchase price is too high for this calculator.',
            ]
        );

        $validator->after(function ($validator) use ($request): void {
            if ($request->boolean('first_time_buyer') && $request->boolean('additional_property')) {
                $validator->errors()->add(
                    'first_time_buyer',
                    'A first-time buyer cannot buy an additional property in this calculator.'
                );
            }
        });

        $validated = $validator->validate();

        $result = $this->sdltCalculatorService->calculate(
            (float) $validated['price'],
            $request->boolean('first_time_buyer'),
            $request->boolean('additional_property')
        );

        return view('sdlt-calculator', [
            'result' => $result,
        ]);
    }
}
