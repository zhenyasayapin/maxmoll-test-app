<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250801053100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE stock (id SERIAL NOT NULL, product_id INT NOT NULL, warehouse_id INT NOT NULL, stock INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4B3656604584665A ON stock (product_id)');
        $this->addSql('CREATE INDEX IDX_4B3656605080ECDE ON stock (warehouse_id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B3656604584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B3656605080ECDE FOREIGN KEY (warehouse_id) REFERENCES warehouse (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock DROP CONSTRAINT FK_4B3656604584665A');
        $this->addSql('ALTER TABLE stock DROP CONSTRAINT FK_4B3656605080ECDE');
        $this->addSql('DROP TABLE stock');
    }
}
