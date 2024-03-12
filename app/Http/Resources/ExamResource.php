<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
     /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'exam';

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'candidateId' => $this->candidate_id,
            'candidateName' => $this->candidate_name,
            'date' => $this->date,
            'locationName' => $this->location_name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude
        ];
    }
}
