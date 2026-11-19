<?php
declare(strict_types=1);
namespace SymSvel\Module\Countries\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use SymSvel\Module\Countries\Service\CountryCatalog;
use SymSvel\Module\Languages\Service\LanguageLocaleResolver;
final readonly class PublicCountriesController {
 public function __construct(private CountryCatalog $countries,private LanguageLocaleResolver $locales) {}
 #[Route('/apiopen/countries',name:'symsvel_countries_public_list',methods:['GET'],format:'json')]
 #[Route('/api/countries',name:'symsvel_countries_app_list',methods:['GET'],format:'json')]
 public function __invoke(Request $request):JsonResponse {
  $response=new JsonResponse(['data'=>$this->countries->publicCountries($this->locales->resolve($request))]);
  $response->headers->set('Cache-Control','no-store');
  return $response;
 }
}
