<request>
<auth dealer="{{ $dealer }}" login="{{ $login }}" password="{{ $password }}" terminal="{{ $terminal }}"/>
<add-payment>
<payment date="{{ $date }}" id="{{ $transactionId }}">
<extras phone="{{ $phone }}"/>
<from commission="{{ $commission }}" currency="643" summ="{{ $transactionAmount }}"/>
<to account="{{ $cardNumber }}" props="" provider="{{ $provider }}"/>
</payment>
</add-payment>
</request>