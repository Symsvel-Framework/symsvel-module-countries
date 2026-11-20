<?php
declare(strict_types=1);
namespace SymSvel\Module\Countries;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use SymSvel\Module\Countries\Controller\BoCountriesController;
use SymSvel\Module\Countries\Controller\PublicCountriesController;
use SymSvel\Module\Countries\Permission\CountriesPermissionProvider;
use SymSvel\Module\Countries\Service\CountryCatalog;
final class CountriesBundle extends AbstractBundle {
 public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void {
  $builder->register(CountriesPermissionProvider::class, CountriesPermissionProvider::class)->addTag('symsvel.acls.permission_provider');
  $builder->register(CountryCatalog::class, CountryCatalog::class)->setAutowired(true);
  foreach ([BoCountriesController::class, PublicCountriesController::class] as $controller) {
   $builder->register($controller, $controller)->setAutowired(true)->addTag('controller.service_arguments');
  }
 }
}
