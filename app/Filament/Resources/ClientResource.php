<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
use Filament\Resources\Resource;
use Filament\Schemas\Schema; 
use Filament\Schemas\Components\Section; 
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn; 
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BackedEnum;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationLabel = 'Clients';

    /**
     * ✅ Filament 4.x: form(Schema $schema): Schema
     */
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Client Information')
                    ->description('Primary contact details.')
                    ->components([ // ✅ v4 izmanto components() nevis schema() iekšpusē
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),

                        TextInput::make('phone')
                            ->tel(),

                        TextInput::make('company'),

                        Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'lead' => 'Lead',
                            ])
                            ->default('active')
                            ->required(),
                    ])->columns(2),

                Section::make('Additional Information')
                    ->components([
                        Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * ✅ Filament 4.x: table(Schema $schema): Schema
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->icon('heroicon-m-envelope')
                    ->copyable()
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        'lead' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
