<?php

use App\Enums\ProjectStatus;

test('project status enum has the correct values', function () {
    expect(ProjectStatus::Idea->value)->toBe('idea')
        ->and(ProjectStatus::InProgress->value)->toBe('in_progress')
        ->and(ProjectStatus::Review->value)->toBe('review')
        ->and(ProjectStatus::Done->value)->toBe('done')
        ->and(ProjectStatus::Cancelled->value)->toBe('cancelled');
});

test('project status enum returns correct labels', function () {
    expect(ProjectStatus::Idea->getLabel())->toBe('Ideja')
        ->and(ProjectStatus::InProgress->getLabel())->toBe('Procesā')
        ->and(ProjectStatus::Done->getLabel())->toBe('Pabeigts');
});

test('project status enum returns correct colors', function () {
    expect(ProjectStatus::Idea->getColor())->toBe('gray')
        ->and(ProjectStatus::InProgress->getColor())->toBe('info')
        ->and(ProjectStatus::Cancelled->getColor())->toBe('danger');
});
