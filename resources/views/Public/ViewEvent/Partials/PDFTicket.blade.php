<!DOCTYPE html>
<html>
<head>
    <!-- Keep this page lean as possible.-->
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <title>
        Ticket(s)
    </title>
    <style type="text/css">
        {!! $css !!}

        .ticket {
            border-color: {{$event->ticket_border_color}}  !important;
            background: {{$event->ticket_bg_color}}  !important;
            color: {{$event->ticket_text_color}}  !important;
        }

        .ticket-checkin {
            border-color: {{$event->ticket_border_color}}  !important;
        }

        .ticket-content .ticket-box-1 .ticket-box-1-col ul li:first-child {
            border-color: {{$event->ticket_border_color}}  !important;
        }

        .ticket-content .ticket-box-1 .ticket-box-1-col ul li {
            border-color: {{$event->ticket_border_color}}  !important;
        }

        .ticket-content .ticket-box-1 .ticket-box-1-col ul li strong {
            color: {{$event->ticket_sub_text_color}}  !important;
        }
    </style>
</head>
<body>

@php
    $ticket_per_page = 2;
    $current_page_tickets = 0;
@endphp

@foreach($attendees as $attendee)
    @if(!$attendee->is_cancelled)
        <div class="ticket grid">
            <div class="col-1-8 ticket-checkin">
                <div>
                    <h3>
                        {{$order->order_reference}}
                    </h3>
                </div>

                <div class="barcode">
                    <img alt="QR Code"
                         src="data:image/png;base64,{!! DNS2D::getBarcodePNG($attendee->private_reference_number, "QRCODE", 6, 6) !!}"
                    />
                </div>

                <div>
                    <h4>
                        {{$attendee->reference}}
                    </h4>
                </div>

                @if($event->is_1d_barcode_enabled)
                    <div class="barcode_vertical">
                        <img alt="Barcode"
                             src="data:image/png;base64,{!! DNS1D::getBarcodePNG($attendee->private_reference_number, "C39+", 1, 50) !!}"
                        />
                    </div>
                @endif

                <div class="organiser_info">
                    <strong>
                        @lang("Ticket.organiser"):
                    </strong>

                    {{$event->organiser->name}}

                    <div class="organiser_logo">
                        <img alt="{{$event->organiser->full_logo_path}}" src="data:image/png;base64, {{$image}}"/>
                    </div>
                </div>
            </div>

            <div class="col-8-1 ticket-content">
                <h3>
                    {{$event->title}}
                </h3>

                <div class="ticket-box-1">
                    <div class="ticket-box-1-col">
                        <div class="ticket-box-info">
                            <ul class="ticket-info">
                                <li class="venue_col">
                                    <strong>
                                        @lang("Ticket.venue")
                                    </strong>
                                    {{$event->venue_name}}
                                </li>
                                <li class="venue_col">
                                    <strong>
                                        @lang("Ticket.start_date_time")
                                    </strong>
                                    {{$event->startDateFormatted()}}
                                </li>
                                <li class="venue_col">
                                    <strong>
                                        @lang("Ticket.end_date_time")
                                    </strong>
                                    {{$event->endDateFormatted()}}
                                </li>
                                <li class="venue_col">
                                    <strong>
                                        @lang("Ticket.name")
                                    </strong>
                                    {{$attendee->first_name.' '.$attendee->last_name}}
                                </li>
                                <li class="venue_col">
                                    <strong>
                                        @lang("Ticket.ticket_type")
                                    </strong>
                                    {{$attendee->ticket->title}}
                                </li>
                                <li class="venue_col">
                                    <strong>
                                        @lang("Ticket.price")
                                    </strong>
                                    @php
                                        // Calculating grand total including tax
                                        $grand_total = $attendee->ticket->total_price;
                                        $tax_amt = ($grand_total * $event->organiser->tax_value) / 100;
                                        $grand_total = $tax_amt + $grand_total;
                                    @endphp
                                    {{money($grand_total, $order->event->currency)}}
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="ticket-box-1-col">
                        <div class="ticket-image">
                            @if(isset($images) && count($images) > 0)
                                @foreach($images as $img)
                                    <img src="data:image/png;base64, {{$img}}"/>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($current_page_tickets % 2)
            <div class="page-break"></div>
        @endif

        @php
            $current_page_tickets++
        @endphp
    @endif
@endforeach

<div class="bottom_info">
    {{--Attendize is provided free of charge on the condition the below hyperlink is left in place.--}}
    {{--See https://www.attendize.com/license.html for more information.--}}
    @include('Shared.Partials.PoweredBy')
</div>
</body>
</html>

