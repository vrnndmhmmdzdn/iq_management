<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms;
use Filament\Forms\Form;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->columns(2)->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->dehydrateStateUsing(fn($state) => bcrypt($state))
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $context) => $context === 'create')
                    ->placeholder('Kosongkan jika tidak ingin mengubah'),

                Forms\Components\Select::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->options(Role::pluck('name', 'id'))
                    ->preload()
                    ->required(),
            ]),
        ]);
    }
}