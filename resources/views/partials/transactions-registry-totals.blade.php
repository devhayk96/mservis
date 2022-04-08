<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-12 text-center h6">Summ transactions</div>

            @foreach ($transactionTotalsByStatus as $statusName => $total)
                <div class="col-4">{{ ucfirst($statusName) }}:  {{ $total }}</div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-4">Transactions:  {{ $transactionsCount }}</div>
            <div class="col-4">Transactions total summ:  {{ $transactionsSum }}</div>
        </div>
    </div>
</div>
