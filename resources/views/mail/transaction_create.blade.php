@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ config('app.name') }}
        @endcomponent
    @endslot

@component('mail::table')
|                                 |                                 |
|---------------------------------|---------------------------------|
| <strong>IP</strong>             | {{ $details['ip'] }}            |
| <strong>User-Agent</strong>     | {{ $details['user_agent'] }}    |
| <strong>Error message</strong>  | {{ $details['error_message'] }} |
| <strong>Method</strong>         | {{ $details['method'] }}        |
| <strong>Path</strong>           | {{ $details['path'] }}          |
| <strong>Amount</strong>         | {{ $details['amount'] }}        |
| <strong>Card Number</strong>    | {{ $details['card_number'] }}   |
| <strong>ID</strong>             | {{ $details['id'] }}            |
| <strong>Token</strong>          | {{ $details['token'] }}         |
@endcomponent

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            <!-- footer here -->
        @endcomponent
    @endslot
@endcomponent
