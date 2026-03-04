<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Category
 */
class CategoryResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'image' => $this->image_url,
            'parent_id' => $this->parent_id,
            'parent' => $this->whenLoaded('parent', fn() => new self($this->parent)),
            'children' => $this->whenLoaded('children', fn() => self::collection($this->children)),
            'sort_order' => $this->sort_order,
            'depth' => $this->getDepth(),
        ];
    }
}
