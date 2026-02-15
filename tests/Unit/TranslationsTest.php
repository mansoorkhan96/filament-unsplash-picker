<?php

it('translates the label to Pick from Unsplash in English', function () {
    app()->setLocale('en');

    expect(__('unsplash-picker::unsplash-picker-action.label'))
        ->toBe('Pick from Unsplash');
});

it('translates search_placeholder to Search photos... in English', function () {
    app()->setLocale('en');

    expect(__('unsplash-picker::unsplash-picker-action.form.fields.search.placeholder'))
        ->toBe('Search photos...');
});

it('translates square_mode label to Square Mode in English', function () {
    app()->setLocale('en');

    expect(__('unsplash-picker::unsplash-picker-action.form.fields.square_mode.label'))
        ->toBe('Square Mode');
});

it('translates next_page to Next in English', function () {
    app()->setLocale('en');

    expect(__('unsplash-picker::unsplash-picker-action.actions.next_page.label'))
        ->toBe('Next');
});

it('translates previous_page to Previous in English', function () {
    app()->setLocale('en');

    expect(__('unsplash-picker::unsplash-picker-action.actions.previous_page.label'))
        ->toBe('Previous');
});

it('translates no_search_results with correct content in English', function () {
    app()->setLocale('en');

    $result = __('unsplash-picker::unsplash-picker-action.no_search_results');

    expect($result)->toContain("didn't return any results");
});

it('translates description with singular count in English', function () {
    app()->setLocale('en');

    $result = trans_choice('unsplash-picker::unsplash-picker-action.description', 1, [
        'numberOfSelectableImages' => 1,
    ]);

    expect($result)->toBe('You may select 1 photo.');
});

it('translates description with plural count in English', function () {
    app()->setLocale('en');

    $result = trans_choice('unsplash-picker::unsplash-picker-action.description', 3, [
        'numberOfSelectableImages' => 3,
    ]);

    expect($result)->toBe('You may select 3 photos.');
});
