<?php

use CodeWithDiki\PaymentModule\Data\PaymentData;
use CodeWithDiki\PaymentModule\Enums\PaymentStatus as EnumsPaymentStatus;
use CodeWithDiki\PaymentModule\Facades\PaymentModule;
use CodeWithDiki\PaymentModule\Models\PaymentMethod;
use CodeWithDiki\ProductModule\Facades\ProductModule;
use CodeWithDiki\ProductModule\Models\Product;
use CodeWithDiki\ProductModule\Models\ProductColor;
use CodeWithDiki\ProductModule\Models\ProductSize;
use CodeWithDiki\TransactionModule\Data\TransactionData;
use CodeWithDiki\TransactionModule\Data\TransactionItemData;
use CodeWithDiki\TransactionModule\Enums\PaymentStatus;
use CodeWithDiki\TransactionModule\Enums\TransactionStatus;
use CodeWithDiki\TransactionModule\Facades\TransactionModule;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    public Product $product;

    public ?ProductColor $selectedColor;

    public ?ProductSize $selectedSize;

    public int $qty = 1;

    public ?PaymentMethod $selectedPaymentMethod = null;

    public ?string $qrPaymentUrl = null;

    public function mount(Product $product)
    {
        $this->product = $product;
        $this->selectedColor = ProductModule::getProductColors($product->id)->where("is_primary")->first();
        $this->selectedSize = ProductModule::getProductSizes($product->id)->where("is_primary")->first();

    }

    public function changeColor(ProductColor $color)
    {
        $this->selectedColor = $color;
    }

    public function changeSize(ProductSize $size)
    {
        $this->selectedSize = $size;
    }

    public function minQty()
    {
        if($this->qty == 1){
            return;
        }

        $this->qty -= 1;
    }

    public function plusQty()
    {
        $this->qty += 1;
    }

    #[Computed]
    public function getPrice()
    {
        return $this->product->discount_price ?? $this->product->price * $this->qty;
    }

    #[Computed]
    public function getPaymentMethods() : Collection
    {
        return PaymentModule::getActivePaymentMethodGroups()->mapWithKeys(function($value, $key){
            return [$value->name => PaymentModule::getPaymentMethodsByGroupId($value->id)];
        });
    }

    public function selectPaymentMethod(PaymentMethod $payment_method)
    {
        $this->selectedPaymentMethod = $payment_method;
    }


    public function checkout()
    {
        try {
            if($this->qty == 0){
                $this->addError("qty", "Minimum quantity for this product is 1!");
                return;
            }
    
            if(empty($this->selectedPaymentMethod)){
                $this->addError("payment_method", "Select payment method first!");
                return;
            }
    
            $date = now();
            $rand = rand(0, 111);
    
            $items = collect([
                new TransactionItemData(
                $this->product, 
                $this->product->name, 
                null, 
                $this->qty, 
                $this->product->discount_price ?? $this->product->price, 
                $this->product->discount_price ?? $this->product->price * $this->qty)
            ]);
    
    
            $transactionData = new TransactionData(
                "TRX-{$date->format('Y-m-d')}-{$rand}",
                TransactionModule::getWalkInCustomer()->id,
                $items->sum("total"),
                PaymentStatus::PENDING,
                TransactionStatus::ONHOLD,
            );
    
            $transaction = TransactionModule::createTransaction($transactionData, $items);
    
            $paymentData = new PaymentData(
                $transaction, 
                $this->selectedPaymentMethod->id, 
                $transactionData->trx_id,
                $transactionData->total_amount,
                EnumsPaymentStatus::PENDING,
            );
    
            $payment = PaymentModule::createPayment($paymentData);

        } catch(\Exception $e)
        {
            
        }

    }

    #[On("echo-private:payment-gateway-processed,.cwd.payment-module.payment-gateway-processed")]
    public function handlePaymentProcessed($event)
    {
        $payment = PaymentModule::getPaymentByCode(data_get($event, "payment.payment_code"));

        $this->qrPaymentUrl = $payment->instruction()->qr_url;
    }

};
?>

<div class="grid grid-cols-3 gap-3">
    <div class="lg:col-span-2">
        <div class="w-full" x-data="bannerUtilities">
            <div class="w-full relative">
                <img :src="banners[current_index]['image_url']" alt="Banner" class="rounded-lg">
                <div class="absolute inset-0 flex">
                    <div class="w-full h-full relative">
                        <template x-if="current_index != 0">
                            <div class="absolute inset-y-0 left-0 h-full flex items-center justify-center px-3">
                                <button class="rounded-full p-2 bg-blue-500/50" @click="prevBanner()">
                                    @svg("heroicon-o-chevron-left", "w-8 h-8")
                                </button>
                            </div>
                        </template>
                        <template x-if="current_index != banners.length -1">
                            <div class="absolute inset-y-0 right-0 h-full flex items-center justify-center px-3">
                                <button class="rounded-full p-2 bg-blue-500/50" @click="nextBanner()">
                                    @svg("heroicon-o-chevron-right", "w-8 h-8")
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="space-y-3">
        <div class="bg-gray-100 p-3 rounded-lg space-y-2">
            <h1 class="text-xl font-bold">
                {{ $product->name }}
            </h1>
            <p class="text-sm">
                {{ $product->description }}
            </p>
            <div class="flex items-center gap-2">
                @forelse(ProductModule::getProductColors($product->id) as $color)
                    <button type="button" wire:click="changeColor({{$color->id}})" 
                    class="px-2 py-1 text-sm font-semibold rounded-md @if($this->selectedColor == $color) bg-zinc-800 text-white @else bg-zinc-800/20 text-white @endif">
                        {{ $color->label }}
                    </button>
                @empty

                @endforelse
            </div>

            <div class="flex items-center gap-2">
                @forelse(ProductModule::getProductSizes($product->id) as $size)
                    <button type="button" wire:click="changeSize({{$size->id}})" 
                    class="px-2 py-1 text-sm font-semibold rounded-md @if($this->selectedSize == $size) bg-zinc-800 text-white @else bg-zinc-800/20 text-white @endif">
                        {{ $size->label }}
                    </button>
                @empty

                @endforelse
            </div>
        </div>
        <div class="bg-gray-100 p-3 rounded-lg space-y-2">
            <h3 class="text-xl font-bold">
                Checkout
            </h3>
            <div class="flex items-center gap-3">
                <button class="p-1 rounded-lg bg-zinc-800 text-white" wire:click="minQty">
                    @svg("heroicon-o-minus", "w-4")
                </button>
                <div>
                    {{ $qty }}
                </div>
                <button class="p-1 rounded-lg bg-zinc-800 text-white" wire:click="plusQty">
                    @svg("heroicon-o-plus", "w-4")
                </button>
            </div>

            <div>
                @foreach ($this->getPaymentMethods as $group => $payment_methods)
                    <div>
                        <h5 class="text-md font-semibold">
                            {{ $group }}
                        </h5>

                        <ul class="">
                            @foreach($payment_methods as $payment_method)
                                <div class="bg-blue-500/20 rounded-md p-3 flex items-center gap-2" wire:click="selectPaymentMethod({{ $payment_method->id }})">
                                    <div class="p-2 rounded-full shrink-0 @if($selectedPaymentMethod == $payment_method) bg-blue-500 @else bg-white @endif"></div>
                                    <div>
                                        {{ $payment_method->name }}
                                    </div>
                                </div>
                            @endforeach
                        </ul>

                    </div>
                @endforeach
            </div>

            <button class="bg-blue-500 block w-full px-3 py-2 rounded-lg text-white font-semibold text-base" wire:click="checkout">
                Rp {{ number_format($this->getPrice, 0, ",", ".") }}
            </button>

            @if($this->qrPaymentUrl)
                <img src="{{ $this->qrPaymentUrl }}" alt="">
            @endif

        </div>
    </div>
</div>

@once
    @push("bottom-js")
        <script>
            function bannerUtilities() {
                return {
                    banners: @js($product->images->map(function($item) {
                        $item->image_url = asset($item->image_url);

                        return $item;
                    })),
                    current_index: 0,

                    nextBanner() {
                        console.log(this.current_index)

                        if (this.current_index != this.banners.length - 1) {
                            this.current_index += 1;
                            return;
                        }

                        this.current_index = 0;
                    },

                    prevBanner() {
                        if (this.current_index == 0) {
                            this.current_index = this.banners.length - 1;
                            return;
                        }

                        this.current_index -= 1;

                    }


                }
            }
        </script>
    @endpush
@endonce