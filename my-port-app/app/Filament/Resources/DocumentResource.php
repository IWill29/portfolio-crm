<?php

namespace App\Filament\Resources;

use App\Models\Document;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use App\Filament\Resources\DocumentResource\Pages;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use BackedEnum;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationLabel = 'Dokumenti';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dokumenta pamatinformācija')
                    ->description('Ievadi dokumenta nosaukumu, tipu un projekta saiti')
                    ->components([
                        TextInput::make('title')
                            ->label('Dokumenta nosaukums')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('piemēram: NDA Līgums'),

                        Select::make('project_id')
                            ->label('Projekts')
                            ->relationship('project', 'title')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('type')
                            ->label('Dokumenta tips')
                            ->options([
                                'contract' => '📋 Līgums',
                                'invoice' => '💵 Rēķins',
                                'specification' => '📐 Specifikācija',
                                'proposal' => '📝 Piedāvājums',
                                'report' => '📊 Ziņojums',
                                'design' => '🎨 Dizains',
                                'other' => '📄 Cits',
                            ])
                            ->required(),

                        Textarea::make('notes')
                            ->label('Piezīmes')
                            ->columnSpanFull()
                            ->placeholder('Pievienojiet jebkādas svarīgas piezīmes par šo dokumentu'),
                    ])->columns(2),

                Section::make('Failu pievienošana')
                    ->description('Augšupielādējiet dokumentu failus (PDF, Word, utt.)')
                    ->components([
                        FileUpload::make('attachments')
                            ->label('Faili')
                            ->multiple()
                            ->disk('public')
                            ->directory('documents')
                            ->maxSize(10240) // 10 MB
                            ->acceptedFileTypes([
                                'application/pdf',
                                'application/msword',
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'image/jpeg',
                                'image/png',
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Nosaukums')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('project.title')
                    ->label('Projekts')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Tips')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'contract' => 'info',
                        'invoice' => 'success',
                        'specification' => 'primary',
                        'proposal' => 'warning',
                        'report' => 'gray',
                        'design' => 'purple',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'contract' => '📋 Līgums',
                        'invoice' => '💵 Rēķins',
                        'specification' => '📐 Specifikācija',
                        'proposal' => '📝 Piedāvājums',
                        'report' => '📊 Ziņojums',
                        'design' => '🎨 Dizains',
                        'other' => '📄 Cits',
                        default => $state,
                    }),

                TextColumn::make('created_at')
                    ->label('Izveidots')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'contract' => '📋 Līgums',
                        'invoice' => '💵 Rēķins',
                        'specification' => '📐 Specifikācija',
                        'proposal' => '📝 Piedāvājums',
                        'report' => '📊 Ziņojums',
                        'design' => '🎨 Dizains',
                        'other' => '📄 Cits',
                    ]),
                SelectFilter::make('project_id')
                    ->relationship('project', 'title'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                // Bulk actions var pievienot citreiz, ja nepieciešams
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
            'view' => Pages\ViewDocument::route('/{record}'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'project.title'];
    }

    public static function getGlobalSearchResultDetails(Document $record): array
    {
        return [
            'Project' => $record->project?->title,
            'Type' => $record->type,
        ];
    }
}
