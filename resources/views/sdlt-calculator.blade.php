<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SDLT Calculator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h1 class="h3 mb-3">Stamp Duty Land Tax (SDLT) Calculator</h1>
                    <p class="text-muted mb-4">Enter your property details to estimate SDLT for England and Northern Ireland.</p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <h2 class="h6">Please fix the following:</h2>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('sdlt.calculate') }}" class="row g-3">
                        @csrf
                        <div class="col-md-6">
                            <label for="price" class="form-label">Property purchase price (GBP)</label>
                            <input
                                type="number"
                                class="form-control @error('price') is-invalid @enderror"
                                id="price"
                                name="price"
                                min="1"
                                step="0.01"
                                value="{{ old('price') }}"
                                required
                            >
                        </div>

                        <div class="col-md-6 d-flex flex-column justify-content-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="first_time_buyer" name="first_time_buyer" {{ old('first_time_buyer') ? 'checked' : '' }}>
                                <label class="form-check-label" for="first_time_buyer">
                                    I am a first-time buyer
                                </label>
                            </div>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" value="1" id="additional_property" name="additional_property" {{ old('additional_property') ? 'checked' : '' }}>
                                <label class="form-check-label" for="additional_property">
                                    This is an additional property
                                </label>
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Calculate SDLT</button>
                        </div>
                    </form>
                </div>
            </div>

            @isset($result)
                <div class="card shadow-sm mt-4">
                    <div class="card-body p-4">
                        <h2 class="h4 mb-3">Calculation result</h2>
                        <p class="mb-1"><strong>Rate scenario:</strong> {{ $result['scenario'] }}</p>
                        <p class="mb-1"><strong>Total SDLT payable:</strong> GBP {{ number_format($result['total_sdlt'], 2) }}</p>
                        <p class="mb-4"><strong>Effective tax rate:</strong> {{ number_format($result['effective_rate'], 2) }}%</p>

                        <h3 class="h5">Tax breakdown</h3>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered align-middle">
                                <thead>
                                <tr>
                                    <th scope="col">Band range</th>
                                    <th scope="col">Amount taxed</th>
                                    <th scope="col">Rate used</th>
                                    <th scope="col">Tax from this band</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($result['breakdown'] as $band)
                                    <tr>
                                        <td>
                                            GBP {{ number_format($band['from'], 0) }}
                                            to
                                            @if (is_null($band['to']))
                                                above
                                            @else
                                                GBP {{ number_format($band['to'], 0) }}
                                            @endif
                                        </td>
                                        <td>GBP {{ number_format($band['taxable_amount'], 2) }}</td>
                                        <td>{{ number_format($band['total_rate'] * 100, 2) }}%</td>
                                        <td>GBP {{ number_format($band['tax_paid'], 2) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endisset
        </div>
    </div>
</main>
</body>
</html>
