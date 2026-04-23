<?php

namespace App\Filament\Resources\Siswas\Schemas;

// use Filament\Forms;
// use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;

use Filament\Forms;
use Filament\Forms\Form;


class SiswaForm
{
    public static function configure(Form $form): Form
    {
        return $form->schema([
            // Gunakan langsung nama class-nya karena sudah di-import di atas
            Section::make('Data Identitas')
                ->columns(2)
                ->schema([
                    TextInput::make('nis')
                        ->label('NIS')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(20),

                    TextInput::make('nisn')
                        ->label('NISN')
                        ->unique(ignoreRecord: true)
                        ->maxLength(20),

                    TextInput::make('nama_lengkap')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(100)
                        ->columnSpanFull(),

                    Select::make('jenis_kelamin')
                        ->label('Jenis Kelamin')
                        ->required()
                        ->options([
                            'L' => 'Laki-laki',
                            'P' => 'Perempuan',
                        ]),

                    Select::make('status')
                        ->label('Status')
                        ->required()
                        ->options([
                            'aktif'    => 'Aktif',
                            'nonaktif' => 'Nonaktif',
                            'lulus'    => 'Lulus',
                        ])
                        ->default('aktif'),
                ]),

            
            Section::make('Data Kelahiran & Alamat')
                ->columns(2)
                ->schema([
                    TextInput::make('tempat_lahir')
                        ->label('Tempat Lahir'),

                    DatePicker::make('tanggal_lahir')
                        ->label('Tanggal Lahir')
                        ->native(false) // Standar Filament terbaru agar UI lebih konsisten
                        ->displayFormat('d/m/Y'),

                    Textarea::make('alamat')
                        ->label('Alamat')
                        ->columnSpanFull()
                        ->rows(3),
                ]),

            Section::make('Data Sekolah')
                ->columns(2)
                ->schema([
                    Select::make('kelas_id')
                        ->label('Kelas')
                        ->relationship('kelas', 'nama_kelas')
                        ->searchable()
                        ->preload(),

                    Select::make('tahun_ajaran_id')
                        ->label('Tahun Ajaran')
                        ->relationship('tahunAjaran', 'nama')
                        ->searchable()
                        ->preload(),

                    FileUpload::make('foto')
                        ->label('Foto')
                        ->image()
                        ->directory('siswa/foto')
                        ->imageEditor() // Fitur editor gambar bawaan v4+
                        ->maxSize(2048)
                        ->columnSpanFull(),
                ]),
        ]); // Titik koma di sini sudah benar untuk menutup return
    }
}
