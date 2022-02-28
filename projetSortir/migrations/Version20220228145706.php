<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220228145706 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE etats (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE inscriptions (id INT AUTO_INCREMENT NOT NULL, id_sortie_id INT NOT NULL, id_participant_id INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_74E0281C4C476574 (id_sortie_id), INDEX IDX_74E0281CA07A8D1F (id_participant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lieux (id INT AUTO_INCREMENT NOT NULL, id_ville_id INT NOT NULL, nom VARCHAR(30) NOT NULL, rue VARCHAR(30) DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, INDEX IDX_9E44A8AEF7E4ECA3 (id_ville_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participants (id INT AUTO_INCREMENT NOT NULL, id_site_id INT NOT NULL, pseudo VARCHAR(30) NOT NULL, nom VARCHAR(30) NOT NULL, prenom VARCHAR(30) NOT NULL, telephone VARCHAR(15) DEFAULT NULL, mail VARCHAR(30) NOT NULL, mdp VARCHAR(30) NOT NULL, administrateur TINYINT(1) NOT NULL, actif TINYINT(1) NOT NULL, INDEX IDX_716970922820BF36 (id_site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sites (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sorties (id INT AUTO_INCREMENT NOT NULL, id_organisateur_id INT NOT NULL, id_lieu_id INT NOT NULL, id_etat_id INT NOT NULL, id_site_id INT NOT NULL, nom VARCHAR(30) NOT NULL, date_debut DATETIME NOT NULL, duree INT DEFAULT NULL, date_cloture_inscription DATETIME NOT NULL, nb_inscriptions_max INT NOT NULL, description_infos LONGTEXT DEFAULT NULL, url_photo LONGTEXT DEFAULT NULL, INDEX IDX_488163E830687172 (id_organisateur_id), INDEX IDX_488163E8B42FBABC (id_lieu_id), INDEX IDX_488163E8D3C32F8F (id_etat_id), INDEX IDX_488163E82820BF36 (id_site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE villes (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, cp VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE inscriptions ADD CONSTRAINT FK_74E0281C4C476574 FOREIGN KEY (id_sortie_id) REFERENCES sorties (id)');
        $this->addSql('ALTER TABLE inscriptions ADD CONSTRAINT FK_74E0281CA07A8D1F FOREIGN KEY (id_participant_id) REFERENCES participants (id)');
        $this->addSql('ALTER TABLE lieux ADD CONSTRAINT FK_9E44A8AEF7E4ECA3 FOREIGN KEY (id_ville_id) REFERENCES villes (id)');
        $this->addSql('ALTER TABLE participants ADD CONSTRAINT FK_716970922820BF36 FOREIGN KEY (id_site_id) REFERENCES sites (id)');
        $this->addSql('ALTER TABLE sorties ADD CONSTRAINT FK_488163E830687172 FOREIGN KEY (id_organisateur_id) REFERENCES participants (id)');
        $this->addSql('ALTER TABLE sorties ADD CONSTRAINT FK_488163E8B42FBABC FOREIGN KEY (id_lieu_id) REFERENCES lieux (id)');
        $this->addSql('ALTER TABLE sorties ADD CONSTRAINT FK_488163E8D3C32F8F FOREIGN KEY (id_etat_id) REFERENCES etats (id)');
        $this->addSql('ALTER TABLE sorties ADD CONSTRAINT FK_488163E82820BF36 FOREIGN KEY (id_site_id) REFERENCES sites (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sorties DROP FOREIGN KEY FK_488163E8D3C32F8F');
        $this->addSql('ALTER TABLE sorties DROP FOREIGN KEY FK_488163E8B42FBABC');
        $this->addSql('ALTER TABLE inscriptions DROP FOREIGN KEY FK_74E0281CA07A8D1F');
        $this->addSql('ALTER TABLE sorties DROP FOREIGN KEY FK_488163E830687172');
        $this->addSql('ALTER TABLE participants DROP FOREIGN KEY FK_716970922820BF36');
        $this->addSql('ALTER TABLE sorties DROP FOREIGN KEY FK_488163E82820BF36');
        $this->addSql('ALTER TABLE inscriptions DROP FOREIGN KEY FK_74E0281C4C476574');
        $this->addSql('ALTER TABLE lieux DROP FOREIGN KEY FK_9E44A8AEF7E4ECA3');
        $this->addSql('DROP TABLE etats');
        $this->addSql('DROP TABLE inscriptions');
        $this->addSql('DROP TABLE lieux');
        $this->addSql('DROP TABLE participants');
        $this->addSql('DROP TABLE sites');
        $this->addSql('DROP TABLE sorties');
        $this->addSql('DROP TABLE villes');
    }
}
