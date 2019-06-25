<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Order extends MyBaseModel
{
    use SoftDeletes;

    /**
     * The validation rules of the model.
     *
     * @var array $rules
     */
    public $rules = [
        'order_first_name' => ['required'],
        'order_last_name'  => ['required'],
        'order_email'      => ['required', 'email'],
    ];

    /**
     * The validation error messages.
     *
     * @var array $messages
     */
    public $messages = [
        'order_first_name.required' => 'Please enter a valid first name',
        'order_last_name.required'  => 'Please enter a valid last name',
        'order_email.email'         => 'Please enter a valid email',
    ];

    protected $casts = [
        'is_business' => 'boolean',
    ];

    /**
     * The items associated with the order.
     *
     * @return HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(\App\Models\OrderItem::class);
    }

    /**
     * The attendees associated with the order.
     *
     * @return HasMany
     */
    public function attendees()
    {
        return $this->hasMany(\App\Models\Attendee::class);
    }

    /**
     * The account associated with the order.
     *
     * @return BelongsTo
     */
    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    /**
     * The event associated with the order.
     *
     * @return BelongsTo
     */
    public function event()
    {
        return $this->belongsTo(\App\Models\Event::class);
    }

    /**
     * The tickets associated with the order.
     *
     * @return HasMany
     */
    public function tickets()
    {
        return $this->hasMany(\App\Models\Ticket::class);
    }


    public function payment_gateway()
    {
        return $this->belongsTo(\App\Models\PaymentGateway::class);
    }

    /**
     * The status associated with the order.
     *
     * @return BelongsTo
     */
    public function orderStatus()
    {
        return $this->belongsTo(\App\Models\OrderStatus::class);
    }


    /**
     * Get the organizer fee of the order.
     *
     * @return Collection|mixed|static
     */
    public function getOrganiserAmountAttribute()
    {
        return $this->amount + $this->organiser_booking_fee + $this->taxamt;
    }

    /**
     * Get the total amount of the order.
     *
     * @return Collection|mixed|static
     */
    public function getTotalAmountAttribute()
    {
        return $this->amount + $this->organiser_booking_fee + $this->booking_fee;
    }

    /**
     * Get the full name of the order.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Boot all of the bootable traits on the model.
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            do {
                //generate a random string using Laravel's str_random helper
                $token = Str::Random(5) . date('jn');
            } //check if the token already exists and if it does, try again

            while (Order::where('order_reference', $token)->first());
            $order->order_reference = $token;

        });
    }
}
