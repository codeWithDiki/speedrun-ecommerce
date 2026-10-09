<?php

use Illuminate\Support\Facades\Route;

Route::livewire("/", "pages::home")->name("home");

Route::prefix("/products")
->name("products.")
->group(function(){

    Route::livewire("/", "pages::product.index")->name("index");

    Route::livewire("/{product:slug}", "pages::product.show")->name("show");

});
