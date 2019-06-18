{!! Html::style(asset('css/ticket.css')) !!}
<style>
    .ticket {
        border-color: {{$event->ticket_border_color}};
        background: {{$event->ticket_bg_color}};
        color: {{$event->ticket_text_color}};
    }

    .ticket-checkin {
        border-color: {{$event->ticket_border_color}};
    }

    .ticket-content .ticket-box-1 .ticket-box-1-col ul li:first-child {
        border-color: {{$event->ticket_border_color}};
    }

    .ticket-content .ticket-box-1 .ticket-box-1-col ul li {
        border-color: {{$event->ticket_border_color}};
    }

    .ticket-content .ticket-box-1 .ticket-box-1-col ul li strong {
        color: {{$event->ticket_sub_text_color}};
    }
</style>

<div class="ticket grid ticket-html">
    <div class="col-1-8 ticket-checkin">
        <div>
            <h3>
                @lang("Ticket.demo_order_ref")
            </h3>
        </div>

        <div class="barcode">
            <img alt="QR Code"
                 src="data:image/png;base64,{!! DNS2D::getBarcodePNG('hello', "QRCODE", 6, 6) !!}"
            />
        </div>

        <div>
            <h4>
                @lang("Ticket.demo_attendee_ref")
            </h4>
        </div>

        @if($event->is_1d_barcode_enabled)
            <div class="barcode_vertical">
                <img alt="Barcode"
                     src="data:image/png;base64,{!! DNS1D::getBarcodePNG(12211221, "C39+", 1, 50) !!}"
                />
            </div>
        @endif

        <div class="organiser_info">
            <strong>
                @lang("Ticket.organiser"):
            </strong>

            @lang("Ticket.demo_organiser")

            <div class="organiser_logo">
                {!! Html::image(asset($image_path)) !!}
            </div>
        </div>
    </div>

    <div class="col-8-1 ticket-content">
        <h3>
            @lang("Ticket.demo_event")
        </h3>

        <div class="ticket-box-1">
            <div class="ticket-box-1-col">
                <div class="ticket-box-info">
                    <ul class="ticket-info">
                        <li class="venue_col">
                            <strong>
                                @lang("Ticket.venue")
                            </strong>
                            @lang("Ticket.demo_venue")
                        </li>
                        <li class="venue_col">
                            <strong>
                                @lang("Ticket.start_date_time")
                            </strong>
                            @lang("Ticket.demo_start_date_time")
                        </li>
                        <li class="venue_col">
                            <strong>
                                @lang("Ticket.end_date_time")
                            </strong>
                            @lang("Ticket.demo_end_date_time")
                        </li>
                        <li class="venue_col">
                            <strong>
                                @lang("Ticket.name")
                            </strong>
                            @lang("Ticket.demo_name")
                        </li>
                        <li class="venue_col">
                            <strong>
                                @lang("Ticket.ticket_type")
                            </strong>
                            @lang("Ticket.demo_ticket_type")
                        </li>
                        <li class="venue_col">
                            <strong>
                                @lang("Ticket.price")
                            </strong>
                            @lang("Ticket.demo_price")
                        </li>
                    </ul>
                </div>
            </div>
            <div class="ticket-box-1-col">
                <div class="ticket-image">
                    {!! Html::image(asset($image_path)) !!}
                </div>
            </div>
        </div>
    </div>
</div>