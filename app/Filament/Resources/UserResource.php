<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Users';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'Master Data';

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->placeholder('Alexander Davinci')->required(),
                TextInput::make('email')->placeholder('alexander@yourdomain.com')->email()->unique()->required(),
                TextInput::make('password')->password()->required()->dehydrateStateUsing(fn($state) => Hash::make($state)),
                Select::make('role')
                    ->options([
                        'Admin' => 'Admin',
                        'Bendahara' => 'Bendahara',
                        'Siswa' => 'Siswa',
                    ])->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable()->sortable()->toggleable(),
                TextColumn::make('email')->label('Email')->searchable()->sortable()->toggleable(),
                TextColumn::make('role')->label('Role')->searchable()->sortable()->toggleable()
            ])
            ->filters([
                Filter::make('Admin')
                    ->query(fn(Builder $query): Builder => $query->where('role', 'Admin')),
                Filter::make('Bendahara')
                    ->query(fn(Builder $query): Builder => $query->where('role', 'Bendahara')),
                Filter::make('Siswa')
                    ->query(fn(Builder $query): Builder => $query->where('role', 'Siswa')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
