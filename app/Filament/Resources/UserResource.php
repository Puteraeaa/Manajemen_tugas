<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Str;


class UserResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            TextInput::make('name')
                    ->required()
                    ->maxLength(255),

            TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),

            TextInput::make('password')
                    ->password()
                    ->required(fn($record) => !$record) // hanya required di create
                    ->minLength(8)
                    ->dehydrated(fn($state) => filled($state)) // supaya password bisa di-hash di model
                    ->label('Password'),

            Select::make('roles')
                    ->relationship('roles', 'name') // Menggunakan relasi 'roles'
                    ->required()
                    ->label('Role')
                    ->multiple(false) // Jika hanya satu role yang diizinkan
                    ->default(fn($record) => $record?->roles->first()?->name ?? null) // Set default value untuk edit
                    ->afterStateUpdated(function ($state, $record) {
                        if ($record) {
                            $record->syncRoles([$state]);
                        }
                    }),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->formatStateUsing(fn($state): string => Str::headline($state))
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
