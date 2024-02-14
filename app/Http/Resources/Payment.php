<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @author Xanders
 * @see https://team.xsamtech.com/xanderssamoth
 */
class Payment extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'provider_reference' => $this->provider_reference,
            'order_number' => $this->order_number,
            'amount' => $this->amount,
            'amount_customer' => $this->amount_customer,
            'phone' => $this->phone,
            'currency' => $this->currency,
            'channel' => $this->channel,
            'subject_url' => $this->subject_url,
            'type' => Type::make($this->type),
            'status' => Status::make($this->status),
            'created_at' => timeAgo($this->created_at->format('Y-m-d H:i:s')),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'user_id' => $this->user_id
        ];
    }
}
