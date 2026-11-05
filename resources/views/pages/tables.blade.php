@extends('layouts.app')

@section('title', 'Tables')

@section('content')
    <div class="grid">
        <div class="grid-item col-12">
            <h5><a href="https://mui.com/components/tables/" target="_blank">MUI Tables</a></h5>
            <p class="text-body2">Tables display sets of data. They can be fully customized</p>
        </div>

        @php
            $desserts = [
                ['name' => 'Frozen yoghurt', 'calories' => 159, 'fat' => 6.0, 'carbs' => 24, 'protein' => 4.0],
                ['name' => 'Ice cream sandwich', 'calories' => 237, 'fat' => 9.0, 'carbs' => 37, 'protein' => 4.3],
                ['name' => 'Eclair', 'calories' => 262, 'fat' => 16.0, 'carbs' => 24, 'protein' => 6.0],
                ['name' => 'Cupcake', 'calories' => 305, 'fat' => 3.7, 'carbs' => 67, 'protein' => 4.3],
                ['name' => 'Gingerbread', 'calories' => 356, 'fat' => 16.0, 'carbs' => 49, 'protein' => 3.9],
            ];
        @endphp

        {{-- Basic table --}}
        <div class="grid-item col-12">
            <div class="card">
                <div class="card-header"><div class="card-header-title" style="font-size: 1.25rem;">Basic Table</div></div>
                <div class="table-wrapper">
                    <table class="table" style="min-width: 650px;" aria-label="simple table">
                        <thead>
                            <tr>
                                <th>Dessert (100g serving)</th>
                                <th class="align-right">Calories</th>
                                <th class="align-right">Fat (g)</th>
                                <th class="align-right">Carbs (g)</th>
                                <th class="align-right">Protein (g)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($desserts as $row)
                                <tr>
                                    <td style="color: var(--text-primary);">{{ $row['name'] }}</td>
                                    <td class="align-right">{{ $row['calories'] }}</td>
                                    <td class="align-right">{{ $row['fat'] }}</td>
                                    <td class="align-right">{{ $row['carbs'] }}</td>
                                    <td class="align-right">{{ $row['protein'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Dense table --}}
        <div class="grid-item col-12">
            <div class="card">
                <div class="card-header"><div class="card-header-title" style="font-size: 1.25rem;">Dense Table</div></div>
                <div class="table-wrapper">
                    <table class="table table-sm" style="min-width: 650px;" aria-label="a dense table">
                        <thead>
                            <tr>
                                <th>Dessert (100g serving)</th>
                                <th class="align-right">Calories</th>
                                <th class="align-right">Fat (g)</th>
                                <th class="align-right">Carbs (g)</th>
                                <th class="align-right">Protein (g)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($desserts as $row)
                                <tr>
                                    <td style="color: var(--text-primary);">{{ $row['name'] }}</td>
                                    <td class="align-right">{{ $row['calories'] }}</td>
                                    <td class="align-right">{{ $row['fat'] }}</td>
                                    <td class="align-right">{{ $row['carbs'] }}</td>
                                    <td class="align-right">{{ $row['protein'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sticky header --}}
        <div class="grid-item col-12">
            <div class="card">
                <div class="card-header"><div class="card-header-title" style="font-size: 1.25rem;">Sticky Header</div></div>
                <div class="card-content" style="padding: 0;">
                    <div class="table-sticky" data-table-pagination>
                        <table class="table" aria-label="sticky table">
                            <thead>
                                <tr>
                                    <th style="min-width: 170px;">Name</th>
                                    <th style="min-width: 100px;">ISO Code</th>
                                    <th class="align-right" style="min-width: 170px;">Population</th>
                                    <th class="align-right" style="min-width: 170px;">Size (km²)</th>
                                    <th class="align-right" style="min-width: 170px;">Density</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ([
                                    ['name' => 'India', 'code' => 'IN', 'population' => 1324171354, 'size' => 3287263],
                                    ['name' => 'China', 'code' => 'CN', 'population' => 1403500365, 'size' => 9596961],
                                    ['name' => 'Italy', 'code' => 'IT', 'population' => 60483973, 'size' => 301340],
                                    ['name' => 'United States', 'code' => 'US', 'population' => 327167434, 'size' => 9833520],
                                    ['name' => 'Canada', 'code' => 'CA', 'population' => 37602103, 'size' => 9984670],
                                    ['name' => 'Australia', 'code' => 'AU', 'population' => 25475400, 'size' => 7692024],
                                    ['name' => 'Germany', 'code' => 'DE', 'population' => 83019200, 'size' => 357578],
                                    ['name' => 'Ireland', 'code' => 'IE', 'population' => 4857000, 'size' => 70273],
                                    ['name' => 'Mexico', 'code' => 'MX', 'population' => 126577691, 'size' => 1972550],
                                    ['name' => 'Japan', 'code' => 'JP', 'population' => 126317000, 'size' => 377973],
                                    ['name' => 'France', 'code' => 'FR', 'population' => 67022000, 'size' => 640679],
                                    ['name' => 'United Kingdom', 'code' => 'GB', 'population' => 67545757, 'size' => 242495],
                                    ['name' => 'Russia', 'code' => 'RU', 'population' => 146793744, 'size' => 17098246],
                                    ['name' => 'Nigeria', 'code' => 'NG', 'population' => 200962417, 'size' => 923768],
                                    ['name' => 'Brazil', 'code' => 'BR', 'population' => 210147125, 'size' => 8515767],
                                ] as $row)
                                    @php $density = $row['population'] / $row['size']; @endphp
                                    <tr>
                                        <td style="color: var(--text-primary);">{{ $row['name'] }}</td>
                                        <td>{{ $row['code'] }}</td>
                                        <td class="align-right">{{ number_format($row['population']) }}</td>
                                        <td class="align-right">{{ number_format($row['size']) }}</td>
                                        <td class="align-right">{{ number_format($density, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="table-pagination">
                        <span>
                            Rows per page:
                            <select data-page-size>
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="100">100</option>
                            </select>
                        </span>
                        <span data-page-label>1–10 of 15</span>
                        <span class="pager">
                            <button type="button" class="icon-btn icon-btn-sm" data-page-prev aria-label="previous page"><x-icon name="ChevronLeft" /></button>
                            <button type="button" class="icon-btn icon-btn-sm" data-page-next aria-label="next page"><x-icon name="ChevronRight" /></button>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Collapsible --}}
        <div class="grid-item col-12">
            <div class="card">
                <div class="card-header"><div class="card-header-title" style="font-size: 1.25rem;">Collapsible Table</div></div>
                <div class="table-wrapper">
                    <table class="table" aria-label="collapsible table">
                        <thead>
                            <tr>
                                <th style="width: 48px;"></th>
                                <th>Dessert (100g serving)</th>
                                <th class="align-right">Calories</th>
                                <th class="align-right">Fat (g)</th>
                                <th class="align-right">Carbs (g)</th>
                                <th class="align-right">Protein (g)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ([
                                ['name' => 'Frozen yoghurt', 'calories' => 159, 'fat' => 6.0, 'carbs' => 24, 'protein' => 4.0, 'price' => 3.99],
                                ['name' => 'Ice cream sandwich', 'calories' => 237, 'fat' => 9.0, 'carbs' => 37, 'protein' => 4.3, 'price' => 4.99],
                                ['name' => 'Eclair', 'calories' => 262, 'fat' => 16.0, 'carbs' => 24, 'protein' => 6.0, 'price' => 3.79],
                                ['name' => 'Cupcake', 'calories' => 305, 'fat' => 3.7, 'carbs' => 67, 'protein' => 4.3, 'price' => 2.5],
                                ['name' => 'Gingerbread', 'calories' => 356, 'fat' => 16.0, 'carbs' => 49, 'protein' => 3.9, 'price' => 1.5],
                            ] as $row)
                                <tr>
                                    <td style="padding-top: 0.5rem; padding-bottom: 0.5rem;">
                                        <button type="button" class="icon-btn icon-btn-sm"
                                                data-collapse-toggle="history-{{ $loop->index }}" aria-expanded="false" aria-label="expand row">
                                            <x-icon name="ChevronDown" size="20" class="chevron-down" />
                                            <x-icon name="ChevronUp" size="20" class="chevron-up d-none" />
                                        </button>
                                    </td>
                                    <td style="color: var(--text-primary);">{{ $row['name'] }}</td>
                                    <td class="align-right">{{ $row['calories'] }}</td>
                                    <td class="align-right">{{ $row['fat'] }}</td>
                                    <td class="align-right">{{ $row['carbs'] }}</td>
                                    <td class="align-right">{{ $row['protein'] }}</td>
                                </tr>
                                <tr>
                                    <td colspan="6" style="padding: 0 !important; border-bottom: 0;">
                                        <div class="collapse" id="history-{{ $loop->index }}">
                                            <div class="p-2">
                                                <h6 style="margin-bottom: 0.5rem;">History</h6>
                                                <table class="table table-sm" aria-label="purchases">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Customer</th>
                                                            <th class="align-right">Amount</th>
                                                            <th class="align-right">Total price ($)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ([['date' => '2020-01-05', 'customer' => '11091700', 'amount' => 3], ['date' => '2020-01-02', 'customer' => 'Anonymous', 'amount' => 1]] as $history)
                                                            <tr>
                                                                <td style="color: var(--text-primary);">{{ $history['date'] }}</td>
                                                                <td>{{ $history['customer'] }}</td>
                                                                <td class="align-right">{{ $history['amount'] }}</td>
                                                                <td class="align-right">{{ round($history['amount'] * $row['price'], 2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Spanning --}}
        @php
            $invoice = collect([
                ['desc' => 'Paperclips (Box)', 'qty' => 100, 'unit' => 1.15],
                ['desc' => 'Paper (Case)', 'qty' => 10, 'unit' => 45.99],
                ['desc' => 'Waste Basket', 'qty' => 2, 'unit' => 17.99],
            ])->map(function ($row) {
                $row['price'] = $row['qty'] * $row['unit'];
                return $row;
            });
            $invoiceSubtotal = $invoice->sum('price');
            $invoiceTaxes = 0.07 * $invoiceSubtotal;
            $invoiceTotal = $invoiceTaxes + $invoiceSubtotal;
        @endphp
        <div class="grid-item col-12">
            <div class="card">
                <div class="card-header"><div class="card-header-title" style="font-size: 1.25rem;">Spanning Table</div></div>
                <div class="table-wrapper">
                    <table class="table" style="min-width: 700px;" aria-label="spanning table">
                        <thead>
                            <tr>
                                <th class="align-center" colspan="3">Details</th>
                                <th class="align-right">Price</th>
                            </tr>
                            <tr>
                                <th>Desc</th>
                                <th class="align-right">Qty.</th>
                                <th class="align-right">Unit</th>
                                <th class="align-right">Sum</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice as $row)
                                <tr>
                                    <td style="color: var(--text-primary);">{{ $row['desc'] }}</td>
                                    <td class="align-right">{{ $row['qty'] }}</td>
                                    <td class="align-right">{{ $row['unit'] }}</td>
                                    <td class="align-right">{{ number_format($row['price'], 2) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td rowspan="3"></td>
                                <td colspan="2">Subtotal</td>
                                <td class="align-right">{{ number_format($invoiceSubtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Tax</td>
                                <td class="align-right">7 %</td>
                                <td class="align-right">{{ number_format($invoiceTaxes, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="2">Total</td>
                                <td class="align-right">{{ number_format($invoiceTotal, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Customized --}}
        <div class="grid-item col-12">
            <div class="card">
                <div class="card-header"><div class="card-header-title" style="font-size: 1.25rem;">Customized Table</div></div>
                <div class="table-wrapper">
                    <table class="table table-customized" style="min-width: 700px;" aria-label="customized table">
                        <thead>
                            <tr>
                                <th>Dessert (100g serving)</th>
                                <th class="align-right">Calories</th>
                                <th class="align-right">Fat (g)</th>
                                <th class="align-right">Carbs (g)</th>
                                <th class="align-right">Protein (g)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($desserts as $row)
                                <tr>
                                    <td style="color: var(--text-primary);">{{ $row['name'] }}</td>
                                    <td class="align-right">{{ $row['calories'] }}</td>
                                    <td class="align-right">{{ $row['fat'] }}</td>
                                    <td class="align-right">{{ $row['carbs'] }}</td>
                                    <td class="align-right">{{ $row['protein'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
