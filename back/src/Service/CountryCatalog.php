<?php
declare(strict_types=1);
namespace SymSvel\Module\Countries\Service;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Symfony\Component\Intl\Countries;
use SymSvel\Module\Settings\Service\SettingManager;
/** Countries always exist; only BO management depends on the multilingual setting. */
final readonly class CountryCatalog {
 public function __construct(private Connection $db, private SettingManager $settings) {}
 public function isAdministrationEnabled(): bool { return $this->settings->get('languages.multilingual') === '1'; }
 /** @return list<array<string,mixed>> */
 public function allForAdministration(string $locale = ''): array { return $this->rows($locale,false); }
 /** @return list<array<string,mixed>> */
 public function publicCountries(string $locale = ''): array { return $this->rows($locale,true); }
 /** @return list<array<string,mixed>> */
 private function rows(string $locale, bool $onlyActive): array {
  $all=$this->db->fetchAllAssociative('SELECT iso_code,name,active,default_currency,official_languages FROM symsvel_countries ORDER BY iso_code');
  $result=[];
  foreach ($all as $row) {
   $item=self::present($row,$locale);
   if (!$onlyActive || $item['active']) $result[]=$item;
  }
  usort($result,static fn(array $a,array $b):int=>strcasecmp($a['name'],$b['name']) ?: strcmp($a['iso_code'],$b['iso_code']));
  return $result;
 }
 /** @param array<string,mixed> $row
  * @return array{iso_code:string,name:string,native_name:string,active:bool,default_currency:?string,official_languages:list<string>} */
 public static function present(array $row,string $locale): array {
  $code=strtoupper((string)$row['iso_code']);
  $native=(string)$row['name'];
  $name=$native;
  $lang=strtolower(explode('-',str_replace('_','-',trim($locale)))[0]);
  if (preg_match('/^[a-z]{2,3}$/D',$lang)===1) {
   try { $translation=Countries::getName($code,$lang); if ($translation!=='') $name=$translation; }
   catch (\Throwable) { /* Unsupported territory or locale: preserve native name. */ }
  }
  $official=json_decode((string)($row['official_languages']??'[]'),true);
  return [
   'iso_code'=>$code,'name'=>$name,'native_name'=>$native,
   'active'=>in_array($row['active'],[true,1,'1','t','true','TRUE'],true),
   'default_currency'=>$row['default_currency']===null ? null : (string)$row['default_currency'],
   'official_languages'=>is_array($official) ? array_values(array_filter($official,'is_string')) : []
  ];
 }
 /** @return array{iso_code:string,active:bool} */
 public function update(string $code,bool $active):array {
  if (preg_match('/^[A-Z]{2}$/D',$code)!==1 || $this->db->fetchOne('SELECT iso_code FROM symsvel_countries WHERE iso_code = ?',[$code])===false)
   throw new \InvalidArgumentException('Unknown country.');
  $this->db->executeStatement('UPDATE symsvel_countries SET active = ? WHERE iso_code = ?',[$active,$code],[ParameterType::BOOLEAN,ParameterType::STRING]);
  return ['iso_code'=>$code,'active'=>$active];
 }
}
