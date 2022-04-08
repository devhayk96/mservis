@extends('layouts.general')

@section('title', 'Реестр транзакций')

@section('additional-css')
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
@endsection

@section('content')
    @csrf

    <form class="row mb-4" id="filter-form" method="GET">

        <div class="col-lg-4">
            <div class="input-group daterange-block input-daterange input-group-sm mb-2">
                <div class="input-group-text">Date from</div>
                <input type="text" class="form-control" name="date_from" autocomplete="off" @if (request()->date_from)value="{{ request()->date_from }}" @endif>
                <div class="input-group-text">to</div>
                <input type="text" class="form-control" name="date_to" autocomplete="off" @if (request()->date_from)value="{{ request()->date_to }}" @endif>
            </div>

            <div class="input-group input-group-sm mb-2">
                <div class="input-group-text">Amount from</div>
                <input type="number" class="form-control" name="amount_from" autocomplete="off" @if (request()->amount_from)value="{{ request()->amount_from }}" @endif>
                <div class="input-group-text">to</div>
                <input type="number" class="form-control" name="amount_to" autocomplete="off" @if (request()->amount_to)value="{{ request()->amount_to }}" @endif>
            </div>

            <div class="input-group input-group-sm mb-2">
                <div class="input-group-text">Status</div>
                <select class="form-select form-select-sm" name="status">
                    @foreach ($statuses as $statusKey => $statusName)
                        <option @if (request()->status == $statusKey)selected @endif value="{{ $statusKey }}">{{ $statusName }}</option>
                    @endforeach
                </select>
            </div>

            @if (auth()->user()->isAdmin())
                <div class="input-group input-group-sm mb-2">
                    <div class="input-group-text">Executor</div>
                    <select class="form-select form-select-sm" name="manager">
                        <option value="0">all</option>
                        @foreach ($managers as $manager)
                            <option @if (request()->has('manager') && request()->get('manager') == $manager) selected @endif value="{{ $manager }}">
                                {{ empty($manager) ? 'None' : $manager }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
        <div class="col-lg-4">
            <input class="form-control form-control-sm mb-2" name="search" placeholder="Search" @if (request()->search)value="{{ request()->search }}" @endif>
        </div>
        <div class="col-lg-4">
            <button type="button" class="btn btn-primary btn-sm action-btn" data-method="GET"
                    data-href="{{ route('transactions-registry.index') }}">Search</button>

            @if (request()->getQueryString())
                <a class="btn btn-secondary btn-sm" href="{{ route('transactions-registry.index') }}">Reset filter</a>
            @endif

            <button type="button" class="btn btn-sm btn-success action-btn" data-method="POST" data-href="{{ route('transactions.registry-export') }}">Export</button>

        </div>
    </form>

    @if($transactions->count() > 0)
        <div class="row totals" data-route="{{ route('transactions-registry.totals') }}">

        </div>

        @if (auth()->user()->isAdmin())
            <form action="{{ route('transactions-processing.send') }}" method="GET" id="processingForm" class="mt-4">
                <div style="display: flex; justify-content: space-between; padding-bottom: 25px;">
                    <div>
                        <input type="checkbox" id="check-all-transactions" style="width: 18px; height: 18px; vertical-align: -2px;"/>
                        <label for="check-all-transactions" style="cursor: pointer; user-select: none;"> Select all transactions</label>
                    </div>

                    <div>
                        <div class="d-flex align-items-center">
                            <label id="select-process" class="me-3">Send transactions to </label>
                            <select class="form-select form-select-sm" id="select-process" name="processingId" style="width: 200px;">
                                @foreach($operators as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            <input type="submit" value="Send" class="btn btn-sm btn-primary ms-3" id="sendProcessBtn" />
                        </div>
                        @if ($errors->has('processingId'))
                            <div class="text-danger">{{ $errors->first('processingId') }}</div>
                        @endif
                    </div>
                </div>
        @endif

        <div class="row">
            <input type="hidden" class="column-sorting" value="{{ $column }}">
            <input type="hidden" class="direction-sorting" value="{{ $direction }}">
            <table class="table">
                <thead>
                    <tr>
                        @foreach($registryColumnNames as $key => $columnName)
                            @if(in_array($key, $registrySortColumnNames))
                                <th class="transaction-header small" data-column="{{ $key }}">{{ $columnName }}
                                    <div class="{{ $column == $key ? $direction == 'asc' ? 'sort-up' : 'sort-down' : 'sort-up'}}"></div>
                                </th>
                            @else
                                <th class="small">{{ $columnName }}</th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $transaction)
                        <tr>
                            @foreach($transaction as $key => $value)
                                @if ($key == 'id')
                                    <td><input type="checkbox" class="transaction-checkbox small" name="transactionsIds[]" value="{{ $value }}"></td>
                                @elseif ($key == 'amount')
                                    <td class="small text-nowrap" data-amount="{{ str_replace([',', ' '], ['.', ''], $value) }}">{{ $value }}</td>
                                @else
                                    <td class="small">{{ $value }}</td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if (auth()->user()->isAdmin())
            </form>
        @endif

        <div class="d-flex justify-content-end">
            {!! $paginator->links() !!}
        </div>
    @else
        <div class="row">
            <div class="col">
                <div class="text-center mt-4">
                    <hr>
                    Транзакций не найдено
                </div>
            </div>
        </div>
    @endif
@endsection

@section('additional-js')
    <script src="/js/registry.js?v={{ time() }}"></script>
@endsection
