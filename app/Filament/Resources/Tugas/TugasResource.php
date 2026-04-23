<?php

namespace App\Filament\Resources\Tugas;

use App\Filament\Resources\Tugas\Pages;
use App\Models\Tugas;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Builder;


class TugasResource extends Resource
{
    protected static ?string $model = Tugas::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Tugas Harian';
    protected static string|\UnitEnum|null $navigationGroup = 'Akademik';
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'Tugas';
    protected static ?string $pluralModelLabel = 'Tugas Harian';

    public static function form(Schema $form): Schema
    {
        return $form->components([
            Section::make('Informasi Tugas')->columns(2)->schema([
                TextInput::make('judul')
                    ->label('Judul Tugas')
                    ->required()
                    ->maxLength(200)
                    ->columnSpanFull(),

                Select::make('mata_pelajaran_id')
                    ->label('Mata Pelajaran')
                    ->relationship('mataPelajaran', 'nama')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        TextInput::make('nama')->label('Nama Mapel')->required(),
                        TextInput::make('kode')->label('Kode'),
                        Select::make('tingkat')->label('Tingkat')
                            ->options(collect(range(1, 6))->mapWithKeys(fn($i) => [$i => "Kelas $i"]))
                            ->required(),
                    ]),

                Select::make('kelas_id')
                    ->label('Untuk Kelas')
                    ->relationship('kelas', 'nama_kelas')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua kelas'),

                DatePicker::make('tanggal')
                    ->label('Tanggal Tugas')
                    ->required()
                    ->default(today())
                    ->displayFormat('d/m/Y'),

                TimePicker::make('batas_waktu')
                    ->label('Batas Waktu')
                    ->seconds(false)
                    ->placeholder('Opsional'),

                Toggle::make('is_aktif')
                    ->label('Aktif / Tampilkan ke Siswa')
                    ->default(true)
                    ->columnSpanFull(),

                Textarea::make('deskripsi')
                    ->label('Deskripsi / Isi Tugas')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),

                FileUpload::make('file_lampiran')
                    ->label('File Lampiran')
                    ->directory('tugas/lampiran')
                    ->acceptedFileTypes(['application/pdf', 'image/*', 'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                    ->maxSize(10240)
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->description(fn($record) => $record->batas_waktu ? 'Batas: '.$record->batas_waktu : null),

                TextColumn::make('judul')
                    ->label('Judul Tugas')
                    ->searchable()
                    ->limit(50)
                    ->description(fn($record) => $record->mataPelajaran->nama ?? 'Umum'),

                TextColumn::make('kelas.nama_kelas')
                    ->label('Kelas')
                    ->badge()
                    ->color('info')
                    ->default('Semua Kelas'),

                TextColumn::make('createdBy.name')
                    ->label('Dibuat oleh')
                    ->toggleable(),

                IconColumn::make('is_aktif')
                    ->label('Aktif')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('gray'),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('tanggal', 'desc')
            ->filters([
                SelectFilter::make('kelas_id')
                    ->label('Kelas')
                    ->relationship('kelas', 'nama_kelas'),

                SelectFilter::make('mata_pelajaran_id')
                    ->label('Mata Pelajaran')
                    ->relationship('mataPelajaran', 'nama'),

                Filter::make('hari_ini')
                    ->label('Tugas Hari Ini')
                    ->query(fn(Builder $query) => $query->whereDate('tanggal', today()))
                    ->toggle(),

                Filter::make('aktif')
                    ->label('Hanya yang Aktif')
                    ->query(fn(Builder $query) => $query->where('is_aktif', true))
                    ->toggle(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTugas::route('/'),
            'create' => Pages\CreateTugas::route('/create'),
            'edit'   => Pages\EditTugas::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::whereDate('tanggal', today())->where('is_aktif', true)->count();
        return $count > 0 ? (string) $count : null;
    }
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->hasRole('guru')) {
            return $query->where('created_by', auth()->id());
        }

        return $query;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    // Auto-set created_by saat create
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }
}