<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Siswa;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Layout;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Http\Livewire\GlobalSearch;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Filament\Resources\SiswaResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SiswaResource\RelationManagers;

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Siswa';
    protected static ?string $title = 'Siswa';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Master Data';
    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nis')->label('NIS')->placeholder('243251212')->required()->unique(),
                TextInput::make('nama')->label('Nama Lengkap')->placeholder('Alexander Davinci')->hidden()->required(),
                Select::make('id_user')
                    ->relationship('idUser', 'name', fn($query) => $query->where('role', 'Siswa'))
                    ->label('Pilih Siswa')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($set, $state) {
                        $user = \App\Models\User::find($state);
                        $set('nama', $user?->name);
                    }),
                Select::make('kelas')->label('Kelas')
                    ->options([
                        'Kelas A' => 'Kelas A',
                        'Kelas B' => 'Kelas B',
                    ])->required(),
                TextInput::make('tmp_lahir')->label('Tempat Lahir')->placeholder('Cianjur')->required(),
                DatePicker::make('tgl_lahir')->label('Tanggal Lahir')->default(now()->setYear(2010))->required(),
                Select::make('gender')->label('Gender')
                    ->options([
                        'Pria' => 'Pria',
                        'Wanita' => 'Wanita',
                    ])->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nis')->label('NIS')->searchable()->sortable()->toggleable(),
                TextColumn::make('nama')->label('Nama')->searchable()->sortable()->toggleable(),
                TextColumn::make('kelas')->label('Kelas')->searchable()->sortable()->toggleable(),
                TextColumn::make('ttl')
                    ->label('TTL')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn($record) => $record->tmp_lahir . ', ' . Carbon::parse($record->tgl_lahir)->format('d-m-Y')),
                TextColumn::make('gender')->label('Gender')->searchable()->sortable()->toggleable(),
            ])
            ->filters(
                [
                    Filter::make('Kelas A')->label('Kelas A')
                        ->query(fn(Builder $query): Builder => $query->where('kelas', 'Kelas A')),
                    Filter::make('Kelas B')->label('Kelas B')
                        ->query(fn(Builder $query): Builder => $query->where('kelas', 'Kelas B')),
                    Filter::make('Pria')
                        ->query(fn(Builder $query): Builder => $query->where('gender', 'Pria')),
                    Filter::make('Wanita')
                        ->query(fn(Builder $query): Builder => $query->where('gender', 'Wanita')),
                ],
            )
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('activate')
                    ->action(fn(Collection $records) => $records->each->activate())
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-o-check'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSiswas::route('/'),
        ];
    }
}
