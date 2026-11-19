<?php
declare(strict_types=1);
namespace SymSvel\Module\Countries\Tests\Unit;
use PHPUnit\Framework\TestCase;
use SymSvel\Module\Countries\Service\CountryCatalog;
final class CountryCatalogPresentationTest extends TestCase {
 public function testFrenchNameAndMetadata():void {
  $row=CountryCatalog::present([
   'iso_code'=>'BE','name'=>'België','active'=>'1',
   'default_currency'=>'EUR','official_languages'=>'["fr","nl","de"]'
  ],'fr-FR');
  self::assertSame('BE',$row['iso_code']);
  self::assertSame('Belgique',$row['name']);
  self::assertSame('België',$row['native_name']);
  self::assertTrue($row['active']);
  self::assertSame('EUR',$row['default_currency']);
  self::assertSame(['fr','nl','de'],$row['official_languages']);
 }
 public function testInvalidLocaleFallsBackAndParsesBoolean():void {
  $row=CountryCatalog::present([
   'iso_code'=>'FR','name'=>'France','active'=>'0',
   'default_currency'=>null,'official_languages'=>'invalid'
  ],'<invalid>');
  self::assertSame('France',$row['name']);
  self::assertFalse($row['active']);
  self::assertSame([],$row['official_languages']);
 }
}
