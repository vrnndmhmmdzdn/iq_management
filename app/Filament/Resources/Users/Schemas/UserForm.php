<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Form $form): Form
    {
        return $form->schema([
            Section::make()->columns(2)->schema([
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->dehydrateStateUsing(fn($state) => bcrypt($state))
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $context) => $context === 'create')
                    ->placeholder('Kosongkan jika tidak ingin mengubah'),

                Select::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->options(Role::pluck('name', 'id'))
                    // ->preload()
                    ->required(),
            ]),
        ]);
    }
}