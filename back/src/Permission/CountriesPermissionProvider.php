<?php
declare(strict_types=1);
namespace SymSvel\Module\Countries\Permission;
use SymSvel\Module\Acls\Permission\PermissionDefinition;
use SymSvel\Module\Acls\Permission\PermissionProviderInterface;
final class CountriesPermissionProvider implements PermissionProviderInterface {
 public function permissions(): iterable {
  yield new PermissionDefinition('countries','list','Consulter les pays');
  yield new PermissionDefinition('countries','manage','Modifier la disponibilité des pays');
 }
}
