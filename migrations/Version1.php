<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version1 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE access_tokens_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE appointments_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE doctors_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE specialisations_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_fav_doctors_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE work_schedules_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE access_tokens (id INT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(1024) NOT NULL, valid_until TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_58D184BCA76ED395 ON access_tokens (user_id)');
        $this->addSql('CREATE UNIQUE INDEX access_tokens_token_uindex ON access_tokens (token)');
        $this->addSql('CREATE TABLE appointments (id INT NOT NULL, patient_id INT DEFAULT NULL, doctor_id INT DEFAULT NULL, type CHAR(1) DEFAULT \'F\' NOT NULL, date DATE NOT NULL, time_from TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, time_to TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, description VARCHAR(64) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6A41727A6B899279 ON appointments (patient_id)');
        $this->addSql('CREATE INDEX IDX_6A41727A87F4FB17 ON appointments (doctor_id)');
        $this->addSql('CREATE TABLE doctors (id INT NOT NULL, specialisation_id INT DEFAULT NULL, user_id INT DEFAULT NULL, title VARCHAR(8) NOT NULL, active BOOLEAN DEFAULT true NOT NULL, appointments_length INT DEFAULT 30 NOT NULL, avatar_filename VARCHAR(64) DEFAULT NULL, address VARCHAR(128) NOT NULL, city VARCHAR(128) NOT NULL, description TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B67687BE5627D44C ON doctors (specialisation_id)');
        $this->addSql('CREATE INDEX IDX_B67687BEA76ED395 ON doctors (user_id)');
        $this->addSql('CREATE TABLE specialisations (id INT NOT NULL, title VARCHAR(64) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE user_fav_doctors (id INT NOT NULL, patient_id INT DEFAULT NULL, doctor_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_607D48B96B899279 ON user_fav_doctors (patient_id)');
        $this->addSql('CREATE INDEX IDX_607D48B987F4FB17 ON user_fav_doctors (doctor_id)');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, doctor_id INT DEFAULT NULL, name VARCHAR(64) NOT NULL, surname VARCHAR(64) NOT NULL, email VARCHAR(256) NOT NULL, phone VARCHAR(16) NOT NULL, password_hash VARCHAR(64) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1483A5E987F4FB17 ON users (doctor_id)');
        $this->addSql('CREATE UNIQUE INDEX users_email_uindex ON users (email)');
        $this->addSql('CREATE TABLE work_schedules (id INT NOT NULL, doctor_id INT DEFAULT NULL, weekday SMALLINT NOT NULL, time_from TIME(0) WITHOUT TIME ZONE NOT NULL, time_to TIME(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_533374DB87F4FB17 ON work_schedules (doctor_id)');
        $this->addSql('ALTER TABLE access_tokens ADD CONSTRAINT FK_58D184BCA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE appointments ADD CONSTRAINT FK_6A41727A6B899279 FOREIGN KEY (patient_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE appointments ADD CONSTRAINT FK_6A41727A87F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctors (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE doctors ADD CONSTRAINT FK_B67687BE5627D44C FOREIGN KEY (specialisation_id) REFERENCES specialisations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE doctors ADD CONSTRAINT FK_B67687BEA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_fav_doctors ADD CONSTRAINT FK_607D48B96B899279 FOREIGN KEY (patient_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_fav_doctors ADD CONSTRAINT FK_607D48B987F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctors (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E987F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctors (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE work_schedules ADD CONSTRAINT FK_533374DB87F4FB17 FOREIGN KEY (doctor_id) REFERENCES doctors (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE appointments DROP CONSTRAINT FK_6A41727A87F4FB17');
        $this->addSql('ALTER TABLE user_fav_doctors DROP CONSTRAINT FK_607D48B987F4FB17');
        $this->addSql('ALTER TABLE users DROP CONSTRAINT FK_1483A5E987F4FB17');
        $this->addSql('ALTER TABLE work_schedules DROP CONSTRAINT FK_533374DB87F4FB17');
        $this->addSql('ALTER TABLE doctors DROP CONSTRAINT FK_B67687BE5627D44C');
        $this->addSql('ALTER TABLE access_tokens DROP CONSTRAINT FK_58D184BCA76ED395');
        $this->addSql('ALTER TABLE appointments DROP CONSTRAINT FK_6A41727A6B899279');
        $this->addSql('ALTER TABLE doctors DROP CONSTRAINT FK_B67687BEA76ED395');
        $this->addSql('ALTER TABLE user_fav_doctors DROP CONSTRAINT FK_607D48B96B899279');
        $this->addSql('DROP SEQUENCE access_tokens_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE appointments_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE doctors_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE specialisations_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_fav_doctors_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE work_schedules_id_seq CASCADE');
        $this->addSql('DROP TABLE access_tokens');
        $this->addSql('DROP TABLE appointments');
        $this->addSql('DROP TABLE doctors');
        $this->addSql('DROP TABLE specialisations');
        $this->addSql('DROP TABLE user_fav_doctors');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE work_schedules');
    }
}
