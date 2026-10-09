<?php

use App\Models\Banner;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    #[Computed]
    public function getBanners(): Collection
    {
        return Banner::where("is_active", true)->get();
    }
};
?>

<div class="w-full" x-data="bannerUtilities">
    <div class="w-full relative">
        <img :src="banners[current_index]['image_url']" alt="Banner" class="rounded-lg">
        <div class="absolute inset-0 flex">
            <div class="w-full h-full relative">
                <a :href="banners[current_index]['target_url']" :target="banners[current_index]['mode']" class="absolute inset-0"></a>
                <div class="absolute inset-y-0 left-0 h-full flex items-center justify-center px-3">
                    <button class="rounded-full p-2 bg-blue-500/50" @click="prevBanner()">
                        @svg("heroicon-o-chevron-left", "w-8 h-8")
                    </button>
                </div>
                <div class="absolute inset-y-0 right-0 h-full flex items-center justify-center px-3">
                    <button class="rounded-full p-2 bg-blue-500/50" @click="nextBanner()">
                        @svg("heroicon-o-chevron-right", "w-8 h-8")
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@once
    @push("bottom-js")
        <script>
            function bannerUtilities(){
                return {
                    banners:@js($this->getBanners()),
                    current_index: 0,

                    init(){
                        
                    },

                    nextBanner(){
                        console.log(this.current_index)

                        if(this.current_index != this.banners.length - 1)
                        {
                            this.current_index += 1;
                            return;
                        }

                        

                        this.current_index = 0;
                    },

                    prevBanner(){
                        if(this.current_index == 0)
                        {
                            this.current_index = this.banners.length -1;
                            return;
                        }

                        this.current_index -= 1;

                    }


                }
            }
        </script>
    @endpush
@endonce