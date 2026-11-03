<?php

declare(strict_types=1);

namespace SymSvel\Module\SymsvelModuleCountries\Tests\Unit;

use PHPUnit\Framework\TestCase;
use SymSvel\Module\SymsvelModuleCountries\SymsvelModuleCountriesBundle;

final class SymsvelModuleCountriesBundleTest extends TestCase
{
    public function testBundleCanBeInstantiated(): void
    {
        self::assertInstanceOf(SymsvelModuleCountriesBundle::class, new SymsvelModuleCountriesBundle());
    }
}
