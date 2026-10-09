<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make("Banner Info")
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        FileUpload::make('image_url')
                            ->image()
                            ->required(),
                        TextInput::make('target_url')
                            ->url()
                            ->required(),
                        Select::make('mode')
                            ->options([
                                "_blank" => "New Tab",
                                "_self" => "Same Tab"
                            ])
                            ->required(),
                        Toggle::make('is_active')
                            ->required(),
                    ])
                    ->columns(1)
                    ->aside()
            ])
            ->columns(1);
    }
}
