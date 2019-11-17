<form class="online_payment ajax" action="<?php echo route('postCreateOrder', ['event_id' => $event->id]); ?>"
      method="post" id="redsys-payment-form">
    <div class="form-row">
        <label for="card-element">
            Credit or debit card
        </label>
        <div id="card-element">

        </div>

        <div id="card-errors" role="alert"></div>
    </div>
    @csrf

    <input class="btn btn-lg btn-success card-submit" style="width:100%;" type="submit" value="Complete Payment">

</form>
<script type="text/javascript">


</script>
<style type="text/css">
</style>
