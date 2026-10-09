<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<nav class="fixed top-0 w-full z-100 bg-white shadow-md py-1">
    <div class="mx-auto max-w-6xl flex justify-between items-center px-2">
        <div class="shrink-0">
            <x-ui.logo-component/>
        </div>
        <ul class="flex items-center gap-2 text-sm font-semibold">
            <li class="relative">
                Products
                <a href="#" class="absolute inset-0" wire:navigate></a>
            </li>
            <li class="relative">
                Carts
                <a href="#" class="absolute inset-0" wire:navigate></a>
            </li>
        </ul>
    </div>
</nav>