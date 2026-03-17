<?php

use App\Models\Client;
use App\Models\Document;
use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('attaches uploaded file to document attachments collection', function () {
    Storage::fake('public');

    $client = Client::factory()->create();

    $project = Project::factory()->create([
        'client_id' => $client->id,
    ]);

    $document = Document::factory()->create([
        'project_id' => $project->id,
        'title' => 'Test Document',
    ]);

    $file = UploadedFile::fake()->create('invoice.pdf', 120);
    $relativePath = $file->store('tmp/documents', 'public');
    $fullPath = Storage::disk('public')->path($relativePath);

    // Simulē CreateDocument afterSave loģiku: pievieno media no diska
    $document->addMedia($fullPath)->toMediaCollection('attachments');

    expect($document->getMedia('attachments')->count())->toBe(1);

    $media = $document->getFirstMedia('attachments');

    expect($media)->not->toBeNull();
    expect(Storage::disk($media->disk ?? 'public')->exists($media->getPathRelativeToRoot()))->toBeTrue();
});

it('updates document fields correctly', function () {
    $client = Client::factory()->create();

    $project = Project::factory()->create([
        'client_id' => $client->id,
    ]);

    $document = Document::factory()->create([
        'project_id' => $project->id,
        'title' => 'Original Title',
        'type' => 'Old type',
        'notes' => 'Old notes',
    ]);

    $document->update([
        'title' => 'Updated Title',
        'type' => 'New type',
        'notes' => 'Updated notes',
    ]);

    $fresh = $document->fresh();

    expect($fresh->title)->toBe('Updated Title')
        ->and($fresh->type)->toBe('New type')
        ->and($fresh->notes)->toBe('Updated notes');
});

it('soft deletes and restores documents', function () {
    $client = Client::factory()->create();

    $project = Project::factory()->create([
        'client_id' => $client->id,
    ]);

    $document = Document::factory()->create([
        'project_id' => $project->id,
    ]);

    $document->delete();
    expect(Document::withTrashed()->find($document->id)->trashed())->toBeTrue();
    expect(Document::count())->toBe(0);

    $document->restore();
    expect(Document::count())->toBe(1);
});

it('keeps project relationship intact for the document', function () {
    $client = Client::factory()->create();

    $project = Project::factory()->create([
        'client_id' => $client->id,
    ]);

    $document = Document::factory()->create([
        'project_id' => $project->id,
    ]);

    expect($document->project)->not->toBeNull();
    expect($document->project->id)->toBe($project->id);
    expect($document->project->client->id)->toBe($client->id);
});