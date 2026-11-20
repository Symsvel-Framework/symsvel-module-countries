<?php
declare(strict_types=1);
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
/** Portable reference table owned by Countries. */
final class Version20261119000010 extends AbstractMigration {
 public function up(Schema $schema): void {
  $table = $schema->createTable('symsvel_countries');
  $table->addColumn('id','integer');
  $table->addColumn('iso_code','string',['length'=>2]);
  $table->addColumn('name','string',['length'=>150]);
  $table->addColumn('active','boolean',['default'=>true]);
  $table->addColumn('default_currency','string',['length'=>3,'notnull'=>false]);
  $table->addColumn('official_languages','text');
  $table->setPrimaryKey(['id']);
  $table->addUniqueIndex(['iso_code'],'uniq_symsvel_countries_iso');
 }
 public function down(Schema $schema): void { $schema->dropTable('symsvel_countries'); }
}
