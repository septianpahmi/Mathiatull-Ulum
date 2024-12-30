<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Pembayaran;
use Illuminate\Support\Str;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use App\Filament\Widgets\DashboardStats;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\BadgeColumn;
use App\Filament\Widgets\PembayaranStats;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\StatsOverviewWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PembayaranResource\Pages;
use App\Filament\Resources\PembayaranResource\RelationManagers;
use App\Filament\Resources\PembayaranResource\Pages\ManagePembayarans;


class PembayaranResource extends Resource
{
    protected static ?string $model = Pembayaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Pembayaran';
    protected static ?string $title = 'Pembayaran';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Transaksi';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('no_pembayaran')
                    ->label('No. Pembayaran')
                    ->default(fn() => 'SPP-' . now()->format('Ymd') . '-' . mt_rand(1000, 9999))
                    ->unique()->required()->disabled(),
                Select::make('id_siswa')
                    ->relationship('idSiswa', 'nama')
                    ->label('Nama Siswa')
                    ->required(),
                TextInput::make('nominal')
                    ->label('Nominal')
                    ->default(70000)
                    ->required()->disabled(),
                DatePicker::make('tgl_tagihan')
                    ->label('Tanggal Tagihan')
                    ->default(now())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_pembayaran')->label('No. Pembayaran')->searchable()->sortable()->toggleable(),
                TextColumn::make('idSiswa.nis')->label('NIS')->searchable()->sortable()->toggleable(),
                TextColumn::make('idSiswa.nama')->label('Nama')->searchable()->sortable()->toggleable(),
                TextColumn::make('idSiswa.kelas')->label('Kelas')->searchable()->sortable()->toggleable(),
                TextColumn::make('nominal')->label('Nominal')->money('IDR', 2)->sortable()->toggleable(),
                BadgeColumn::make('status')->label('Status')
                    ->colors([
                        'primary' => 'Menunggu Pembayaran',
                        'success' => 'Berhasil',
                    ])->searchable()->sortable()->toggleable(),
                TextColumn::make('tgl_tagihan')
                    ->label('Tanggal Tagihan')
                    ->date()->searchable()->sortable()->sortable()->toggleable(),
                TextColumn::make('tgl_pembayaran')
                    ->label('Tanggal Pembayaran')
                    ->date()->searchable()->sortable()->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Menunggu Pembayaran' => 'Menunggu Pembayaran',
                        'Berhasil' => 'Berhasil',
                    ])
                    ->label('Status Pembayaran'),
                SelectFilter::make('kelas')
                    ->relationship('idSiswa', 'kelas')
                    ->label('Kelas'),
            ])
            ->actions([
                Action::make('changeStatus')
                    ->label('Ubah Status')->hidden(fn($record) => $record->status === 'Berhasil')
                    ->color('primary')
                    ->icon('heroicon-o-adjustments')
                    ->form([
                        Select::make('status')
                            ->options([
                                'Berhasil' => 'Berhasil',
                            ])
                            ->required(),
                    ])
                    ->action(function (Pembayaran $record, array $data) {
                        $record->update([
                            'status' => $data['status'],
                        ]);
                        if ($data['status'] === 'Berhasil') {
                            $record->update([
                                'tgl_pembayaran' => Carbon::parse($record->tgl_pembayaran)->now(),
                            ]);
                            $newTanggalTagihan = Carbon::parse($record->tgl_tagihan)->addMonth();

                            Pembayaran::create([
                                'no_pembayaran' => 'SPP-' . Carbon::now()->addMonth()->format('Ymd') . '-' . mt_rand(1000, 9999),
                                'nominal' => $record->nominal,
                                'tgl_tagihan' => $newTanggalTagihan,
                                'status' => 'Menunggu Pembayaran',
                                'id_siswa' => $record->id_siswa,
                            ]);
                        }
                    })->visible(auth()->user()->role === 'Admin'),
                Tables\Actions\ActionGroup::make([
                    Action::make('downloadInvoice')
                        ->label('Invoice')
                        ->icon('heroicon-o-download')->color('danger')
                        ->action(function ($record) {
                            if ($record->status === 'Berhasil') {
                                // Generate PDF
                                $pdf = Pdf::loadView('filament.pages.invoice', [
                                    'pembayaran' => $record,
                                    'user' => $record->idSiswa,
                                ]);

                                // Nama file invoice
                                $filename = 'invoice-' . $record->no_pembayaran . '.pdf';

                                // Simpan PDF ke folder
                                $path = Storage::disk('public')->put("invoices/$filename", $pdf->output());

                                return response()->download(storage_path("app/public/invoices/$filename"));
                            } else {
                                Notification::make()
                                    ->title('Invoice Tidak Tersedia')
                                    ->body('Invoice hanya tersedia untuk transaksi dengan status "Berhasil".')
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->visible(fn($record) => $record->status === 'Berhasil'),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    public static function getEloquentQuery(): Builder
    {
        if (auth()->check() && auth()->user()->role === 'Siswa') {
            return parent::getEloquentQuery()
                ->whereHas('idSiswa', function ($query) {
                    $query->where('id_user', auth()->id());
                });
        }
        return parent::getEloquentQuery();
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePembayarans::route('/'),
        ];
    }
}
