<?php

namespace App\Filament\Resources\Siswas;

use App\Filament\Resources\Siswas\Pages;
use App\Models\Siswa;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\IconEntry;

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Data Siswa';
    protected static string|\UnitEnum|null $navigationGroup = 'Akademik';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Siswa';
    protected static ?string $pluralModelLabel = 'Data Siswa';

    public static function form(Schema $form): Schema
    {
        return $form->components([
            Section::make('Data Identitas')->columns(2)->schema([
                TextInput::make('nis')->label('NIS')->required()->unique(ignoreRecord: true)->maxLength(20),
                TextInput::make('nisn')->label('NISN')->unique(ignoreRecord: true)->maxLength(20),
                TextInput::make('nama_lengkap')->label('Nama Lengkap')->required()->maxLength(100)->columnSpanFull(),
                Select::make('jenis_kelamin')->label('Jenis Kelamin')->required()->options(['L' => 'Laki-laki', 'P' => 'Perempuan']),
                Select::make('status')->label('Status')->required()->options(['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif', 'lulus' => 'Lulus'])->default('aktif'),
            ]),

            Section::make('Data Kelahiran & Alamat')->columns(2)->schema([
                TextInput::make('tempat_lahir')->label('Tempat Lahir'),
                DatePicker::make('tanggal_lahir')->label('Tanggal Lahir')->displayFormat('d/m/Y'),
                Textarea::make('alamat')->label('Alamat')->columnSpanFull()->rows(3),
            ]),

            Section::make('Data Sekolah')->columns(2)->schema([
                Select::make('kelas_id')->label('Kelas')->relationship('kelas', 'nama_kelas')->searchable()->preload(),
                Select::make('tahun_ajaran_id')->label('Tahun Ajaran')->relationship('tahunAjaran', 'nama')->searchable()->preload(),
                FileUpload::make('foto')->label('Foto')->image()->directory('siswa/foto')->maxSize(2048)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')->label('')->circular()
                    ->defaultImageUrl(fn($record) => 'https://ui-avatars.com/api/?name='.urlencode($record->nama_lengkap).'&color=3b82f6&background=dbeafe'),
                TextColumn::make('nama_lengkap')->label('Nama Siswa')->searchable()->sortable()->description(fn($record) => 'NIS: '.$record->nis),
                TextColumn::make('kelas.nama_kelas')->label('Kelas')->badge()->color('info'),
                TextColumn::make('jenis_kelamin')->label('JK')->badge()->color(fn($state) => $state === 'L' ? 'info' : 'danger'),
                TextColumn::make('status')->label('Status')->badge()->color(fn($state) => match($state) {
                    'aktif' => 'success', 'nonaktif' => 'gray', 'lulus' => 'info', default => 'gray',
                }),
                IconColumn::make('spp_lunas')->label('SPP')
                    ->getStateUsing(fn($record) => $record->isSppLunas())
                    ->boolean()->trueIcon('heroicon-o-check-circle')->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')->falseColor('danger'),
            ])
            ->filters([
                SelectFilter::make('kelas_id')->label('Kelas')->relationship('kelas', 'nama_kelas'),
                SelectFilter::make('status')->options(['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif', 'lulus' => 'Lulus']),
            ])
            ->recordActions([ViewAction::make(), EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('nama_lengkap');
    }

    public static function infolist(Schema $infolist): Schema
    {
        return $infolist->components([
            Section::make('Data Pribadi')->columns(2)->schema([
                TextEntry::make('nama_lengkap')->label('Nama Lengkap'),
                TextEntry::make('nis')->label('NIS'),
                TextEntry::make('nisn')->label('NISN')->default('-'),
                TextEntry::make('jenis_kelamin')->label('Jenis Kelamin')->formatStateUsing(fn($state) => $state === 'L' ? 'Laki-laki' : 'Perempuan'),
                TextEntry::make('tempat_lahir')->label('Tempat Lahir')->default('-'),
                TextEntry::make('tanggal_lahir')->label('Tanggal Lahir')->date('d M Y')->default('-'),
                TextEntry::make('alamat')->label('Alamat')->default('-')->columnSpanFull(),
            ]),
            Section::make('Data Sekolah')->columns(2)->schema([
                TextEntry::make('kelas.nama_kelas')->label('Kelas')->default('-'),
                TextEntry::make('tahunAjaran.nama')->label('Tahun Ajaran')->default('-'),
                TextEntry::make('status')->label('Status')->badge()->color(fn($state) => match($state) {
                    'aktif' => 'success', 'nonaktif' => 'gray', 'lulus' => 'info', default => 'gray',
                }),
                IconEntry::make('spp_lunas')->label('SPP')->getStateUsing(fn($record) => $record->isSppLunas())->boolean(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSiswas::route('/'),
            'create' => Pages\CreateSiswa::route('/create'),
            'view'   => Pages\ViewSiswa::route('/{record}'),
            'edit'   => Pages\EditSiswa::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'aktif')->count();
    }
}