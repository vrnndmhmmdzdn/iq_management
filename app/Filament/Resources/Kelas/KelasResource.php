<?php

namespace App\Filament\Resources\Kelas;

use App\Filament\Resources\Kelas\Pages;
use App\Models\Kelas;
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
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;

class KelasResource extends Resource
{
    protected static ?string $model = Kelas::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationLabel = 'Data Kelas';
    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Kelas';
    protected static ?string $pluralModelLabel = 'Data Kelas';

    public static function form(Schema $form): Schema
    {
        return $form->components([
            Section::make()->columns(2)->schema([
                TextInput::make('nama_kelas')
                    ->label('Nama Kelas')
                    ->required()
                    ->placeholder('Contoh: Kelas 1A')
                    ->maxLength(50),

                Select::make('tingkat')
                    ->label('Tingkat')
                    ->required()
                    ->options(collect(range(1, 6))->mapWithKeys(fn($i) => [$i => "Kelas $i"])),

                Select::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->relationship('tahunAjaran', 'nama')
                    ->required()
                    ->searchable()
                    ->preload(),

                Select::make('wali_kelas_id')
                    ->label('Wali Kelas')
                    ->options(User::role('guru')->pluck('name', 'id'))
                    ->searchable()
                    ->nullable()
                    ->placeholder('Pilih wali kelas'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tingkat')
                    ->label('Tingkat')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn($state) => "Kelas $state")
                    ->sortable(),

                TextColumn::make('nama_kelas')
                    ->label('Nama Kelas')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tahunAjaran.nama')
                    ->label('Tahun Ajaran')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('waliKelas.name')
                    ->label('Wali Kelas')
                    ->default('Belum ditentukan'),

                TextColumn::make('siswa_count')
                    ->label('Jumlah Siswa')
                    ->counts('siswa')
                    ->badge()
                    ->color('success'),
            ])
            ->filters([
                SelectFilter::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->relationship('tahunAjaran', 'nama'),

                SelectFilter::make('tingkat')
                    ->label('Tingkat')
                    ->options(collect(range(1, 6))->mapWithKeys(fn($i) => [$i => "Kelas $i"])),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListKelas::route('/'),
            'create' => Pages\CreateKelas::route('/create'),
            'edit'   => Pages\EditKelas::route('/{record}/edit'),
        ];
    }
}