<?php

declare(strict_types=1);

namespace SymSvel\Module\Countries\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SymSvel\Module\Countries\CountriesBundle;

final class CountriesBundleTest extends TestCase
{
    public function testBundleCanBeInstantiated(): void
    {
        self::assertInstanceOf(CountriesBundle::class, new CountriesBundle());
    }
}
