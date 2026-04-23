<?php

namespace App\Filament\Resources\TahunAjarans;

use App\Filament\Resources\TahunAjarans\Pages;
use App\Models\TahunAjaran;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class TahunAjaranResource extends Resource
{
    protected static ?string $model = TahunAjaran::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Tahun Ajaran';
    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Tahun Ajaran';

    public static function form(Schema $form): Schema
    {
        return $form->components([
            Section::make()->columns(2)->schema([
                TextInput::make('nama')->label('Tahun Ajaran')->required()->placeholder('Contoh: 2024/2025')->columnSpanFull(),
                DatePicker::make('tanggal_mulai')->label('Tanggal Mulai')->required()->displayFormat('d/m/Y'),
                DatePicker::make('tanggal_selesai')->label('Tanggal Selesai')->required()->displayFormat('d/m/Y'),
                Toggle::make('is_aktif')->label('Jadikan Aktif')->helperText('Hanya satu tahun ajaran yang bisa aktif')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')->label('Tahun Ajaran')->searchable()->sortable(),
                TextColumn::make('tanggal_mulai')->label('Mulai')->date('d M Y'),
                TextColumn::make('tanggal_selesai')->label('Selesai')->date('d M Y'),
                IconColumn::make('is_aktif')->label('Aktif')->boolean(),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTahunAjarans::route('/'),
            'create' => Pages\CreateTahunAjaran::route('/create'),
            'edit'   => Pages\EditTahunAjaran::route('/{record}/edit'),
        ];
    }
}