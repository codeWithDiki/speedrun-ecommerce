<?php

use CodeWithDiki\ProductModule\Facades\ProductModule;
use CodeWithDiki\ProductModule\Models\Product;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    #[Computed]
    public function getProducts() : Collection
    {
        return Product::all();
    }


};
?>

<div class="grid grid-cols-3 gap-3">
    @forelse($this->getProducts() as $product)
        <div class="bg-white rounded-lg">
            <figure class="relative">
                <img src="{{ $product->primary_image_url }}" alt="" class="rounded-lg">
                <div class="absolute inset-0 flex flex-col items-center justify-end">
                    <div class="w-full px-3 py-2 space-y-1 bg-blue-500/30 rounded-b-lg">
                        <h3 class="text-lg font-semibold">
                            {{ $product->name }}
                        </h3>
                        <p class="text-xs">
                            {{ $product->description }}
                        </p>
                        <a href="{{ route("products.show", $product) }}" class="px-3 py-2 inline-block rounded-md bg-blue-500 text-white text-xs">
                            <div class="flex items-center gap-1">
                                <div>
                                    View Product
                                </div>
                                @svg("heroicon-o-arrow-long-right", "w-3 h-3")
                            </div>
                        </a>
                    </div>
                </div>
            </figure>
        </div>
    @empty
        <div class="col-span-3 bg-gray-200 flex items-center justify-center h-48 flex-col rounded-md gap-3">
            <div>
                @svg("heroicon-o-exclamation-circle", "w-20")
            </div>
            <h4 class="text-xl font-semibold">
                No Products Is Available Right Now!
            </h4>
            <p>
                Please comeback later!
            </p>
        </div>
    @endforelse
</div>