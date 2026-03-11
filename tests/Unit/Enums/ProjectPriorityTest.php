<?php

use App\Enums\ProjectPriority;

test('project priority enum has the correct values', function () {
    expect(ProjectPriority::Low->value)->toBe('low')
        ->and(ProjectPriority::Medium->value)->toBe('medium')
        ->and(ProjectPriority::High->value)->toBe('high')
        ->and(ProjectPriority::Urgent->value)->toBe('urgent');
});

test('project priority enum returns correct labels', function () {
    expect(ProjectPriority::Low->getLabel())->toBe('Zema')
        ->and(ProjectPriority::Urgent->getLabel())->toBe('Steidzami');
});

test('project priority enum returns correct colors', function () {
    expect(ProjectPriority::Low->getColor())->toBe('gray')
        ->and(ProjectPriority::Urgent->getColor())->toBe('danger');
});
