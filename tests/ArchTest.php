<?php

it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

it('does not use env() helper directly')
    ->expect('env')
    ->not->toBeUsed();
