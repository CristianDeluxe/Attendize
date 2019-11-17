<section class="payment_gateway_options" id="gateway_{{$payment_gateway['id']}}">
    <h4>@lang('PaymentGateway_Redsys.settings')</h4>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('redsys[merchantName]', trans('PaymentGateway_Redsys.merchant_name'), array('class'=>'control-label ')) !!}
                {!! Form::text('redsys[merchantName]', $account->getGatewayConfigVal($payment_gateway['id'], 'merchantName'),[ 'class'=>'form-control'])  !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('redsys[merchantCode]', trans('PaymentGateway_Redsys.merchant_code'), array('class'=>'control-label ')) !!}
                {!! Form::text('redsys[merchantCode]', $account->getGatewayConfigVal($payment_gateway['id'], 'merchantCode'),[ 'class'=>'form-control'])  !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('redsys[merchantKey]', trans('PaymentGateway_Redsys.merchant_key'), array('class'=>'control-label ')) !!}
                {!! Form::text('redsys[merchantKey]', $account->getGatewayConfigVal($payment_gateway['id'], 'merchantKey'),[ 'class'=>'form-control'])  !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('redsys[test]', trans('PaymentGateway_Redsys.test'), array('class'=>'control-label ')) !!}
                {!! Form::checkbox('redsys[test]', $account->getGatewayConfigVal($payment_gateway['id'], 'test', null),[ 'class'=>'form-control'])  !!}
            </div>
            <div class="form-group">
                {!! Form::label('redsys[https]', trans('PaymentGateway_Redsys.https'), array('class'=>'control-label ')) !!}
                {!! Form::checkbox('redsys[https]', $account->getGatewayConfigVal($payment_gateway['id'], 'test', null),[ 'class'=>'form-control'])  !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('redsys[terminal]', trans('PaymentGateway_Redsys.terminal'), array('class'=>'control-label ')) !!}
                {!! Form::text('redsys[terminal]', $account->getGatewayConfigVal($payment_gateway['id'], 'terminal'),[ 'class'=>'form-control', 'placeholder' => 1])  !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('redsys[TransactionType]', trans('PaymentGateway_Redsys.transaction_type'), array('class'=>'control-label ')) !!}
                {!! Form::text('redsys[TransactionType]', $account->getGatewayConfigVal($payment_gateway['id'], 'TransactionType'),[ 'class'=>'form-control', 'placeholder' => 0])  !!}
            </div>
        </div>
    </div>
</section>
