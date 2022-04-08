<request>
<auth dealer="{{ $dealer }}" login="{{ $login }}" password="{{ $password }}" terminal="{{ $terminal }}"/>
<check-payment>
<payment id="{{ $transactionId }}" rate="1">
<extras phone="{{ $phone }}"/>
<from commission="{{ $commission }}" currency="643" summ="{{ $transactionAmount }}"/>
<to account="{{ $cardNumber }}" provider="{{ $provider }}"/>
</payment>
</check-payment>
</request>

