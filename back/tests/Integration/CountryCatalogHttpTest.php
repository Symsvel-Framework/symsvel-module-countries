<?php

declare(strict_types=1);

namespace SymSvel\Module\Countries\Tests\Integration;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use SymSvel\Module\Acls\Access\BoAccessDecision;
use SymSvel\Module\Acls\Access\BoAccessGate;
use SymSvel\Module\Countries\Controller\BoCountriesController;
use SymSvel\Module\Countries\Controller\PublicCountriesController;
use SymSvel\Module\Countries\Service\CountryCatalog;
use SymSvel\Module\Languages\Service\LanguageLocaleResolver;
use SymSvel\Module\Languages\Service\LanguagesSettingDefinitionProvider;
use SymSvel\Module\Settings\Entity\Setting;
use SymSvel\Module\Settings\Repository\SettingRepository;
use SymSvel\Module\Settings\Service\SettingKeyQualifier;
use SymSvel\Module\Settings\Service\SettingManager;
use SymSvel\Module\Settings\Service\SettingRegistry;
use SymSvel\Module\Settings\Service\SettingSecretCipher;
use Symfony\Component\HttpFoundation\Request;

final class CountryCatalogHttpTest extends TestCase
{
    public function testPublicAvailabilityAndBoMultilingualAclLifecycle(): void
    {
        if (!extension_loaded('pdo_sqlite')) {
            self::markTestSkipped('pdo_sqlite required for Countries integration.');
        }
        $config = ORMSetup::createAttributeMetadataConfiguration([
            dirname((new \ReflectionClass(Setting::class))->getFileName()),
        ], true);
        $config->enableNativeLazyObjects(true);
        $db = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        $em = new EntityManager($db, $config);
        try {
            (new SchemaTool($em))->createSchema([$em->getClassMetadata(Setting::class)]);
            $registry = new SettingRegistry([]);
            $registry->registerOwned(new LanguagesSettingDefinitionProvider(), 'languages', new SettingKeyQualifier());
            $managerRegistry = $this->createMock(ManagerRegistry::class);
            $managerRegistry->method('getManagerForClass')->willReturn($em);
            $settings = new SettingManager(
                $registry, new SettingRepository($managerRegistry), $em,
                new SettingSecretCipher('countries-integration-secret-key'),
            );
            $settings->set('languages.primary_language', 'fr');
            $settings->set('languages.multilingual', false);
            $settings->flush();

            $db->executeStatement(
                'CREATE TABLE symsvel_countries (id INTEGER PRIMARY KEY, iso_code VARCHAR(2) NOT NULL UNIQUE, '
                .'name VARCHAR(150) NOT NULL, active BOOLEAN NOT NULL, default_currency VARCHAR(3) NULL, '
                .'official_languages TEXT NOT NULL)',
            );
            foreach ([
                [1, 'FR', 'France', true, 'EUR', '["fr"]'],
                [2, 'BE', 'België', true, 'EUR', '["fr","nl","de"]'],
                [3, 'JP', '日本', false, 'JPY', '["ja"]'],
            ] as [$id, $code, $name, $enabled, $currency, $languages]) {
                $db->executeStatement(
                    'INSERT INTO symsvel_countries (id, iso_code, name, active, default_currency, official_languages) '
                    .'VALUES (?, ?, ?, ?, ?, ?)',
                    [$id, $code, $name, $enabled, $currency, $languages],
                    [
                        ParameterType::INTEGER, ParameterType::STRING, ParameterType::STRING,
                        ParameterType::BOOLEAN, ParameterType::STRING, ParameterType::STRING,
                    ],
                );
            }
            $gate = new class implements BoAccessGate {
                public bool $manage = false;
                public bool $list = true;

                public function check(Request $request, string $resource, string $action): BoAccessDecision
                {
                    if ($resource !== 'countries') {
                        throw new \LogicException('Unexpected ACL resource.');
                    }
                    return $action === 'list' && $this->list
                        || $action === 'manage' && $this->manage
                        ? new BoAccessDecision(200, 7, 'ROLE_ADMIN')
                        : new BoAccessDecision(403);
                }
            };
            $catalog = new CountryCatalog($db, $settings);
            $locale = new LanguageLocaleResolver();
            $bo = new BoCountriesController($gate, $catalog, $locale);
            $public = new PublicCountriesController($catalog, $locale);
            $request = Request::create('/apibo/countries?translation_lang=fr');
            self::assertSame(404, $bo->list($request)->getStatusCode());
            self::assertSame(403, $bo->update(self::patch('JP', true), 'JP')->getStatusCode());
            $gate->manage = true;
            self::assertSame(404, $bo->update(self::patch('JP', true), 'JP')->getStatusCode());
            $gate->manage = false;

            // Public reference data must remain usable on a monolingual project.
            $publicResponse = $public(Request::create('/apiopen/countries?translation_lang=fr'));
            self::assertSame(200, $publicResponse->getStatusCode());
            $publicData = self::payload($publicResponse)['data'];
            self::assertCount(2, $publicData);
            self::assertSame(['BE', 'FR'], array_column($publicData, 'iso_code'));
            self::assertSame('Belgique', $publicData[0]['name']);
            self::assertSame('EUR', $publicData[0]['default_currency']);

            $settings->set('languages.multilingual', true);
            $settings->flush();
            $response = $bo->list($request);
            self::assertSame(200, $response->getStatusCode());
            self::assertTrue($response->headers->hasCacheControlDirective('no-store'));
            self::assertCount(3, self::payload($response)['data']);
            self::assertFalse(self::payload($response)['can_manage']);
            self::assertSame(403, $bo->update(self::patch('JP', true), 'JP')->getStatusCode());

            $gate->manage = true;
            self::assertSame(400, $bo->update(
                Request::create('/apibo/countries/JP', 'PATCH', [], [], [], ['CONTENT_TYPE' => 'application/json'],
                    '{"active":"true"}'),
                'JP',
            )->getStatusCode());
            self::assertSame(404, $bo->update(self::patch('XX', true), 'XX')->getStatusCode());
            self::assertSame(200, $bo->update(self::patch('JP', true), 'JP')->getStatusCode());
            self::assertSame(1, (int) $db->fetchOne('SELECT active FROM symsvel_countries WHERE iso_code = ?', ['JP']));
            self::assertCount(3, self::payload($public(Request::create('/api/countries')))['data']);
            self::assertSame(200, $bo->update(self::patch('JP', false), 'JP')->getStatusCode());
            self::assertCount(2, self::payload($public(Request::create('/api/countries')))['data']);

            $gate->list = false;
            self::assertSame(403, $bo->list($request)->getStatusCode());
            self::assertSame(3, (int) $db->fetchOne('SELECT COUNT(*) FROM symsvel_countries'));
        } finally {
            $db->close();
        }
    }

    private static function patch(string $code, bool $active): Request
    {
        return Request::create('/apibo/countries/'.$code, 'PATCH', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['active' => $active], JSON_THROW_ON_ERROR));
    }

    /** @return array<string, mixed> */
    private static function payload(\Symfony\Component\HttpFoundation\Response $response): array
    {
        return json_decode((string) $response->getContent(), true, 16, JSON_THROW_ON_ERROR);
    }
}
