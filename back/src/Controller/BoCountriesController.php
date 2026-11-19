<?php
declare(strict_types=1);
namespace SymSvel\Module\Countries\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use SymSvel\Module\Acls\Access\BoAccessGate;
use SymSvel\Module\Countries\Service\CountryCatalog;
use SymSvel\Module\Languages\Service\LanguageLocaleResolver;
final readonly class BoCountriesController {
 public function __construct(private BoAccessGate $gate,private CountryCatalog $countries,private LanguageLocaleResolver $locales) {}
 #[Route('/apibo/countries',name:'symsvel_countries_bo_list',methods:['GET'],format:'json')]
 public function list(Request $request):JsonResponse {
  if (($error=$this->check($request,'list'))!==null) return $error;
  if (!$this->countries->isAdministrationEnabled()) return self::fail(404);
  return self::reply(['data'=>$this->countries->allForAdministration($this->locales->resolve($request)),
   'can_manage'=>$this->gate->check($request,'countries','manage')->allowed()]);
 }
 #[Route('/apibo/countries/{code<[A-Z]{2}>}',name:'symsvel_countries_bo_update',methods:['PATCH'],format:'json')]
 public function update(Request $request,string $code):JsonResponse {
  if (($error=$this->check($request,'manage'))!==null) return $error;
  if (!$this->countries->isAdministrationEnabled()) return self::fail(404);
  if (!str_starts_with(strtolower($request->headers->get('Content-Type','')),'application/json') || strlen($request->getContent())>1024)
   return self::fail(400);
  try { $data=json_decode($request->getContent(),true,4,JSON_THROW_ON_ERROR); }
  catch (\JsonException) { return self::fail(400); }
  if (!is_array($data)||array_is_list($data)||count($data)!==1||!array_key_exists('active',$data)||!is_bool($data['active']))
   return self::fail(400);
  try { return self::reply(['data'=>$this->countries->update($code,$data['active'])]); }
  catch (\InvalidArgumentException) { return self::fail(404); }
 }
 private function check(Request $request,string $action):?JsonResponse {
  $decision=$this->gate->check($request,'countries',$action);
  return $decision->allowed()?null:self::fail($decision->status);
 }
 private static function fail(int $status):JsonResponse {
  return self::reply(['error'=>['code'=>match($status){
   400=>'invalid_request',401=>'unauthorized',403=>'forbidden',404=>'not_found',default=>'unavailable'
  }]],$status);
 }
 private static function reply(array $data,int $status=200):JsonResponse {
  $response=new JsonResponse($data,$status);
  $response->headers->set('Cache-Control','no-store');
  return $response;
 }
}
