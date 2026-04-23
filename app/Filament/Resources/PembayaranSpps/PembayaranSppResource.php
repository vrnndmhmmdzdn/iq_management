<?php

namespace App\Filament\Resources\PembayaranSpps;

use App\Filament\Resources\PembayaranSpps\Pages;
use App\Models\PembayaranSpp;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;

class PembayaranSppResource extends Resource
{
    protected static ?string $model = PembayaranSpp::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Konfirmasi SPP';
    protected static string|\UnitEnum|null $navigationGroup = 'Keuangan';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Pembayaran SPP';
    protected static ?string $pluralModelLabel = 'Pembayaran SPP';

    public static function form(Schema $form): Schema
    {
        return $form->components([
            Section::make()->columns(2)->schema([
                Select::make('siswa_id')->label('Siswa')->relationship('siswa', 'nama_lengkap')->searchable()->preload()->required(),
                TextInput::make('periode')->label('Periode (YYYY-MM)')->required()->placeholder('2025-01'),
                TextInput::make('nominal')->label('Nominal')->required()->numeric()->prefix('Rp'),
                Select::make('status')->label('Status')->options(['menunggu' => 'Menunggu', 'dikonfirmasi' => 'Dikonfirmasi', 'ditolak' => 'Ditolak'])->required(),
                Textarea::make('catatan_admin')->label('Catatan Admin')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('siswa.nama_lengkap')->label('Siswa')->searchable()->sortable()
                    ->description(fn($record) => $record->siswa->kelas->nama_kelas ?? '-'),
                TextColumn::make('periode')->label('Periode')
                    ->formatStateUsing(fn($state) => \Carbon\Carbon::createFromFormat('Y-m', $state)->locale('id')->translatedFormat('F Y')),
                TextColumn::make('nominal')->label('Nominal')->money('IDR'),
                TextColumn::make('status')->label('Status')->badge()->color(fn($state) => match($state) {
                    'menunggu' => 'warning', 'dikonfirmasi' => 'success', 'ditolak' => 'danger', default => 'gray',
                })->formatStateUsing(fn($state) => match($state) {
                    'menunggu' => 'Menunggu', 'dikonfirmasi' => 'Lunas', 'ditolak' => 'Ditolak', default => '-',
                }),
                TextColumn::make('created_at')->label('Dikirim')->dateTime('d M Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(['menunggu' => 'Menunggu', 'dikonfirmasi' => 'Dikonfirmasi', 'ditolak' => 'Ditolak']),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('konfirmasi')->label('Konfirmasi')->icon('heroicon-o-check-circle')->color('success')
                    ->visible(fn($record) => $record->status === 'menunggu')
                    ->requiresConfirmation()->modalHeading('Konfirmasi Pembayaran')
                    ->form([Textarea::make('catatan_admin')->label('Catatan (opsional)')])
                    ->action(fn($record, array $data) => $record->update([
                        'status' => 'dikonfirmasi', 'catatan_admin' => $data['catatan_admin'] ?? null,
                        'dikonfirmasi_oleh' => auth()->id(), 'dikonfirmasi_at' => now(),
                    ])),
                Action::make('tolak')->label('Tolak')->icon('heroicon-o-x-circle')->color('danger')
                    ->visible(fn($record) => $record->status === 'menunggu')
                    ->requiresConfirmation()->modalHeading('Tolak Pembayaran')
                    ->form([Textarea::make('catatan_admin')->label('Alasan Penolakan')->required()])
                    ->action(fn($record, array $data) => $record->update([
                        'status' => 'ditolak', 'catatan_admin' => $data['catatan_admin'],
                        'dikonfirmasi_oleh' => auth()->id(), 'dikonfirmasi_at' => now(),
                    ])),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $infolist): Schema
    {
        return $infolist->components([
            Section::make('Info Siswa')->columns(2)->schema([
                TextEntry::make('siswa.nama_lengkap')->label('Nama Siswa'),
                TextEntry::make('siswa.nis')->label('NIS'),
                TextEntry::make('siswa.kelas.nama_kelas')->label('Kelas')->default('-'),
                TextEntry::make('user.name')->label('Diajukan Oleh'),
            ]),
            Section::make('Detail Pembayaran')->columns(2)->schema([
                TextEntry::make('periode')->label('Periode')
                    ->formatStateUsing(fn($state) => \Carbon\Carbon::createFromFormat('Y-m', $state)->locale('id')->translatedFormat('F Y')),
                TextEntry::make('nominal')->label('Nominal')->money('IDR'),
                TextEntry::make('status')->label('Status')->badge()->color(fn($state) => match($state) {
                    'menunggu' => 'warning', 'dikonfirmasi' => 'success', 'ditolak' => 'danger', default => 'gray',
                }),
                TextEntry::make('catatan_ortu')->label('Catatan Ortu')->default('-'),
                TextEntry::make('catatan_admin')->label('Catatan Admin')->default('-'),
                TextEntry::make('dikonfirmasiOleh.name')->label('Dikonfirmasi Oleh')->default('-'),
                TextEntry::make('dikonfirmasi_at')->label('Dikonfirmasi Pada')->dateTime('d M Y H:i')->default('-'),
            ]),
            Section::make('Bukti Pembayaran')->schema([
                ImageEntry::make('bukti_pembayaran')->label('')->disk('public'),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPembayaranSpps::route('/'),
            'view'  => Pages\ViewPembayaranSpp::route('/{record}'),
            'edit'  => Pages\EditPembayaranSpp::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'menunggu')->count();
        return $count > 0 ? (string) $count : null;
    }
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}