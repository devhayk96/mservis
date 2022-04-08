@extends('layouts.general')

@section('title', 'Главная')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Export</h5>
                    <h6 class="card-subtitle mb-4 text-muted">Download file</h6>

                    <form method="POST" action="{{ route('transactions.export') }}">
                        @csrf

                        <div class="input-group daterange-block input-daterange input-group-sm mb-4">
                            <div class="input-group-text">Date from</div>
                            <input type="text" class="form-control" name="date_from" autocomplete="off">
                            <div class="input-group-text">to</div>
                            <input type="text" class="form-control" name="date_to" autocomplete="off">
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm">Download</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-body js--import-block">
                    <h5 class="card-title">Import</h5>
                    <h6 class="card-subtitle mb-2 text-muted">Load Data</h6>

                    <form method="POST" action="{{ route('transactions.check-file') }}" enctype="multipart/form-data" class="js--upload-form">
                        @csrf

                        <div class="mb-3">
                            <label for="importFile" class="custom-file-upload btn-sm">
                               Choose file
                            </label>
                            <input name="import_file" type="file" id="importFile" accept=".xlsx" style="display: none;"/>
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm">Upload file</button>
                    </form>

                    <div class="js--import-messages-container">

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('additional-js')
    <script src="/js/sync.js?v={{ time() }}"></script>
@endsection
