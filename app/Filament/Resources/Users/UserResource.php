<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Manajemen User';
    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'User';

    public static function form(Schema $form): Schema
    {
        return $form->components([
            Section::make()->columns(2)->schema([
                TextInput::make('name')->label('Nama')->required()->maxLength(255),
                TextInput::make('email')->label('Email')->email()->required()->unique(ignoreRecord: true),
                TextInput::make('password')->label('Password')->password()
                    ->dehydrateStateUsing(fn($state) => bcrypt($state))
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $context) => $context === 'create')
                    ->placeholder('Kosongkan jika tidak ingin mengubah'),
                Select::make('roles')->label('Role')->relationship('roles', 'name')
                    ->options(Role::pluck('name', 'id'))->preload()->required(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('roles.name')->label('Role')->badge()->color(fn($state) => match($state) {
                    'admin' => 'purple', 'guru' => 'success', 'ortu' => 'info', default => 'gray',
                }),
                TextColumn::make('created_at')->label('Dibuat')->dateTime('d M Y')->sortable(),
            ])
            ->filters([SelectFilter::make('roles')->relationship('roles', 'name')->label('Role')])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}