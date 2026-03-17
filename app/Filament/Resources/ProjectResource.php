<?php

namespace App\Filament\Resources;

use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use App\Models\Project;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use App\Filament\Resources\ProjectResource\Pages; 
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Facades\Auth;
use BackedEnum;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';
    
    protected static ?string $navigationLabel = 'Projekti';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Projekta pamatinformācija')
                    ->components([
                        TextInput::make('title')
                            ->label('Nosaukums')
                            ->required()
                            ->maxLength(255),

                        Select::make('client_id')
                            ->label('Klients')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Textarea::make('description')
                            ->label('Apraksts')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Statuss un Finanses')
                    ->components([
                        Select::make('status')
                            ->label('Statuss')
                            ->options(ProjectStatus::class)
                            ->required(),

                        Select::make('priority')
                            ->label('Prioritāte')
                            ->options(ProjectPriority::class)
                            ->required(),

                        DatePicker::make('starts_at')
                            ->label('Sākuma datums'),

                        DatePicker::make('ends_at')
                            ->label('Beigu datums'),

                        TextInput::make('budget')
                            ->label('Budžets')
                            ->numeric()
                            ->prefix('€')
                            ->visible(fn () => Gate::allows('view financial data'))
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Nosaukums')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('client.name')
                    ->label('Klients')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('priority')
                    ->badge(),

                TextColumn::make('budget')
                    ->label('Budžets')
                    ->money('EUR')
                    ->visible(fn () => Gate::allows('view financial data'))
                    ->sortable(),

                TextColumn::make('ends_at')
                    ->label('Termiņš')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(ProjectStatus::class),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn () => Gate::allows('delete records')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
