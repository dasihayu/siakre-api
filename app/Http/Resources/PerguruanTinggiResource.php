<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PerguruanTinggiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_pt' => $this->kode_pt,
            'nama_pt' => $this->nama_pt,
            'alamat' => $this->alamat,
            'pimpinan' => $this->pimpinan,
            'visi' => $this->visi,
            'misi' => $this->misi,
            'sk_pendirian' => $this->sk_pendirian,
            'tanggal_sk_pendirian' => $this->tanggal_sk_pendirian?->toDateString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
