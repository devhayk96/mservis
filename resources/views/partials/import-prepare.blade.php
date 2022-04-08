<div class="mt-4">
    <div>Будет импортировано транзакций: {{ $validRowsAmount }} </div>
    <div>Всего в файле транзакций: {{ $totalRowsAmount }}</div>

    <form action="{{ route('transactions.import') }}" method="POST" class="mt-2 js--confirm-form">
        @csrf
        <input type="hidden" name="file_name" value="{{ $fileName }}">

        <button type="submit" class="btn btn-primary">Провести</button>
        <button type="button" class="btn btn-secondary js--cancel-button">Отменить</button>
    </form>
</div>