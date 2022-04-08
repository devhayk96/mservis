@extends('layouts.general')

@section('title', 'Admin Finance Module')

@section('additional-css')
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
@endsection

@section('content')

    <div class="row row-cols-1 row-cols-md-2 g-4">
        <div class="col">
            <form class="card h-100 mb-3" action="{{ route('admin-finance-module.set-commission') }}" method="POST">
                <div class="card-body">
                    <div class="card-title h5">Commission management</div>

                    @if ($errors && $errors->getBag('commission')->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->getBag('commission')->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @csrf

                    <div class="row">
                        <div class="col-6 col-xl-2">
                            Date
                        </div>
                        <div class="col-6 col-xl-3">
                            <input type="text" class="form-control form-control-sm mb-3 date-input" name="date" autocomplete="off" value="{{ old('date') }}">
                        </div>
                        <div class="col-6 col-xl-3 offset-xl-1">
                            Percentage
                        </div>
                        <div class="col-6 col-xl-3">
                            <input class="form-control form-control-sm mb-3" type="number" name="amount" min="0" step="0.01" autocomplete="off"  value="{{ old('amount') }}">
                        </div>
                        <div class="col-6 col-xl-2">
                            Merchant
                        </div>
                        <div class="col-6 col-xl-4">
                            <select class="form-select form-select-sm mb-3" name="merchant_id">
                                <option disabled selected value style="display: none;"></option>
                                @foreach ($merchants as $merchant)
                                    <option value="{{ $merchant->id }}">
                                        {{ $merchant->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-end pt-0">
                    <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                </div>
            </form>
        </div>

        <div class="col">
            <form class="card h-100 mb-3 balance-form" action="{{ route('admin-finance-module.set-merchant-balance') }}" method="POST">
                <div class="card-body">
                    <div class="card-title h5">Balance management</div>

                    @if ($errors && $errors->getBag('balance')->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->getBag('balance')->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @csrf

                    <div class="row">
                        <div class="col-6 col-xl-2">
                            Merchant
                        </div>
                        <div class="col-6 col-xl-4">
                            <select class="form-select form-select-sm mb-3 merchant-with-balance" name="merchant_id">
                                <option disabled selected value style="display: none;"></option>
                                @foreach ($merchants as $merchant)
                                    <option value="{{ $merchant->id }}" data-balance="{{ $merchant->balance }}">
                                        {{ $merchant->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-xl-3">
                            Current Balance:
                        </div>
                        <div class="col-6 col-xl-3 mb-3 current-balance-value">

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6">
                            <div class="row">
                                <div class="col-6 col-xl-4 text-start">
                                    Date
                                </div>
                                <div class="col-6 col-xl-6">
                                    <input type="text" class="form-control form-control-sm mb-3 date-input" name="date" autocomplete="off" value="{{ old('date') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 col-xl-4">
                                    Comment
                                </div>
                                <div class="col-6 col-xl-8">
                                    <textarea class="form-control mb-3" id="exampleFormControlTextarea1" rows="3" name="comment">{{ old('comment') }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="row">
                                <div class="col-4 col-xl-6">
                                    Rate
                                </div>
                                <div class="col-8 col-xl-6">
                                    <input class="form-control form-control-sm mb-3" type="number" name="rate" min="0" step="0.01" autocomplete="off" value="{{ old('rate') }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-4 col-xl-6">
                                    Commission
                                </div>
                                <div class="col-8 col-xl-6">
                                    <input class="form-control form-control-sm mb-3" type="number" name="commission" min="0" step="0.01" autocomplete="off" value="{{ old('commission') }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-4 col-xl-6">
                                    Sum
                                </div>
                                <div class="col-8 col-xl-6">
                                    <input class="form-control form-control-sm mb-3" type="number" name="sum" min="0" step="0.01" autocomplete="off" value="{{ old('sum') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col text-center">
                            <div>
                                Total amount (RUB): <span class="total-amount">{{ old('total_amount') ?: '0,00' }}</span>
                            </div>
                            <div>
                                <input type="hidden" name="total_amount" value="{{ old('total_amount') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-end pt-0">
                    <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('additional-js')
    <script src="/js/admin-finance.js?v={{ time() }}"></script>
@endsection
