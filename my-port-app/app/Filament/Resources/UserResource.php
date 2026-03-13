<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use App\Filament\Resources\UserResource\Pages;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use BackedEnum;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Lietotāji';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Lietotāja pamatinformācija')
                    ->description('Ievadi lietotāja vārdu un e-pastu')
                    ->components([
                        TextInput::make('name')
                            ->label('Pilnais vārds')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('piemēram: Jānis Bērziņš'),

                        TextInput::make('email')
                            ->label('E-pasts')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('janis@example.com'),

                        TextInput::make('password')
                            ->label('Parole')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => $state ? Hash::make($state) : null)
                            ->required(fn (string $operation) => $operation === 'create')
                            ->nullable()
                            ->placeholder('Tikai uz izveidi un atjauninājumu'),
                    ])->columns(2),

                Section::make('Lomas un atļaujas')
                    ->description('Piešķir lietotājam lomas un atļaujas')
                    ->components([
                        Select::make('roles')
                            ->label('Lomas')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->options(fn () => Role::pluck('name', 'name'))
                            ->preload()
                            ->searchable(),

                        Select::make('permissions')
                            ->label('Tieši piešķirtās atļaujas')
                            ->multiple()
                            ->relationship('permissions', 'name')
                            ->preload()
                            ->searchable()
                            ->helperText('Ja iespējams, izmanto tikai lomas'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Vārds')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('E-pasts')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),

                TextColumn::make('roles.name')
                    ->label('Lomas')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'manager' => 'warning',
                        default => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->separator(','),

                TextColumn::make('created_at')
                    ->label('Reģistrēts')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->options(fn () => Role::pluck('name', 'name')->toArray()),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                // Bulk actions citreiz
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }
}
