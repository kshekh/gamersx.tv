<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220927180544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('Update home_row_item SET isPublishedStart = CASE WHEN `isPublishedStart` - 28800 < 0 THEN (`isPublishedStart` - 28800) + 86400
                            ELSE `isPublishedStart` - 28800 END,
                         isPublishedEnd = CASE WHEN `isPublishedEnd` - 28800 < 0 THEN (`isPublishedEnd` - 28800) + 86400
                            ELSE `isPublishedEnd` - 28800 END;');
        $this->addSql('Update home_row SET isPublishedStart = CASE WHEN `isPublishedStart` - 28800 < 0 THEN (`isPublishedStart` - 28800) + 86400
                            ELSE `isPublishedStart` - 28800 END,
                         isPublishedEnd = CASE WHEN `isPublishedEnd` - 28800 < 0 THEN (`isPublishedEnd` - 28800) + 86400
                            ELSE `isPublishedEnd` - 28800 END;');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('Update home_row_item SET isPublishedStart = CASE WHEN `isPublishedStart` + 28800 >= 86400 THEN (`isPublishedStart` + 28800) - 86400
                            ELSE `isPublishedStart` + 28800 END,
                         isPublishedEnd = CASE WHEN `isPublishedEnd` + 28800 > 86400 THEN (`isPublishedEnd` + 28800) - 86400
                            ELSE `isPublishedEnd` + 28800 END;'
        );
        $this->addSql('Update home_row SET isPublishedStart = CASE WHEN `isPublishedStart` + 28800 >= 86400 THEN (`isPublishedStart` + 28800) - 86400
                            ELSE `isPublishedStart` + 28800 END,
                         isPublishedEnd = CASE WHEN `isPublishedEnd` + 28800 > 86400 THEN (`isPublishedEnd` + 28800) - 86400
                            ELSE `isPublishedEnd` + 28800 END;'
        );
    }
}
