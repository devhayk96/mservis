@extends('layouts.general')

@section('title', 'Admin Finance Module')

@section('additional-css')
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
@endsection

@section('content')
    <div class="row">
        <form class="mb-4" action="{{ route('merchant-finance-module.filter') }}" method="POST">
            @csrf

            <div class="row input-daterange">
                <div class="col-lg-2">
                    <label class="form-label mb-0">Interval (from):</label>
                    <input type="text" class="form-control form-control-sm text-start" name="date_from" autocomplete="off" @if (request()->date_from)value="{{ request()->date_from }}" @endif>
                </div>
                <div class="col-lg-2">
                    <label class="form-label mb-0">Interval (to):</label>
                    <input type="text" class="form-control form-control-sm text-start" name="date_to" autocomplete="off" @if (request()->date_to)value="{{ request()->date_to }}" @endif>
                </div>
                <div class="col-lg-2 pt-4">
                    <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                    @if (request()->getQueryString())
                        <a class="btn btn-secondary btn-sm" href="{{ route('merchant-finance-module.index') }}">Reset filter</a>
                    @endif
                </div>
            </div>
        </form>

    </div>

    <div class="row mb-4">
        <div class="col">
            <div class="card h-100">
                <div class="card-body text-center d-flex justify-content-center align-items-center">
                    <strong>Balance: <br>@money($balance)</strong>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-body text-center d-flex justify-content-center align-items-center">
                    <strong>Commission: <br>{{ $commission->amount }}%</strong>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-body text-center d-flex justify-content-center align-items-center">
                    <strong>Total <br>transaction sum: <br>@money($totalAmount)</strong>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-body text-center d-flex justify-content-center align-items-center">
                    <strong>Total <br>commission sum:<br>@money($totalCommissions)</strong>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100">
                <div class="card-body text-center d-flex justify-content-center align-items-center">
                    <strong>Total <br>commission + transaction <br>sum:<br>@money($totalAmountAndCommissions)</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="h5 text-center" style="margin-top: 50px;">Debits form balance Log</div>

    @if($transactions->count() > 0)

        <table class="table mb-4">
            <thead>
                <tr>
                    @foreach($columnNames as $name)
                        <th class="small">{{ $name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $transaction)
                    <tr>
                        @foreach($transaction as $value)
                            <td class="small">{{ $value }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="d-flex justify-content-end">
            {!! $paginator->links() !!}
        </div>
    @else
        <div class="text-center">
            <hr>
            No records for today
        </div>
    @endif

@endsection

@section('additional-js')
    <script src="/js/merchant-finance.js?v={{ time() }}"></script>
@endsection
