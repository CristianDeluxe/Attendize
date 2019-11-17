<form class="online_payment ajax" action="<?php echo route('postCreateOrder', ['event_id' => $event->id]); ?>"
      method="post" id="redsys-payment-form">
    @csrf

    <div class="form-row" style="padding-bottom: 10px">
        {{ trans('PaymentGateway_Redsys.credit_card') }}
    </div>

    <input class="btn btn-lg btn-success card-submit" style="width:100%;" type="submit"
           value="{{ trans('PaymentGateway_Redsys.complete_payment') }}">
</form>