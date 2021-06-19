<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use phpDocumentor\Reflection\Types\This;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210619065043 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'This migration add User in database';
    }

    public function up(Schema $schema): void
    {
        $date = date("Y-m-d H:i:s");
        $hashedPassword = '$argon2id$v=19$m=65536,t=4,p=1$aCNP6O1Y5t+M0JmQubiy4w$1G3mo7stqKlHSTZ4fIRfYHLbQZIGpuJ+rv3ex9AKEuA';
        
        $this->addSql("INSERT INTO `group` VALUES('1', 'Vp-13');");
        $this->addSql("
            INSERT INTO user VALUES (
                null,
                '1',
                '',
                '{$hashedPassword}',
                'Test',
                '{$date}',
                '{$date}',
                'User',
                'User2002'
            );
        ");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
