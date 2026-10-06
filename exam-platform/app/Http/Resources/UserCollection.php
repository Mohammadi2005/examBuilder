<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data'=> [
                "total_items"   => $this->total(),
                'results' => UserResource::collection($this->collection),
                'hasPrevPage' => !$this->onFirstPage(),
                'hasNextPage' => $this->hasMorePages(),
                'page' => $this->currentPage(),
                'total_page' => $this->lastPage(),
                'total_products' => $this->total(),
            ],
            'statusCode' => 200,
            'success' => true,
            'message' => "موفقیت آمیز",
            'errors' => null

        ];
    }
    public function paginationInformation($request, $paginated, $default)
    {
        unset($default['links']);
        unset($default['meta']);

        return $default;
    }
}
