DROP TABLE IF EXISTS giocate;
DROP TABLE IF EXISTS utenti;
DROP TABLE IF EXISTS partite;
DROP TABLE IF EXISTS squadre;


CREATE TABLE IF NOT EXISTS squadre(
	id BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	nome VARCHAR(20) NOT NULL
);

CREATE TABLE IF NOT EXISTS partite(
	id BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	squadra1 BIGINT UNSIGNED NOT NULL REFERENCES squadre(id),
	squadra2 BIGINT UNSIGNED NOT NULL REFERENCES squadre(id),
	data_partita DATE NOT NULL,
	G1 float NOT NULL,
	GX float NOT NULL,
	G2 float NOT NULL,
	GGoal float NOT NULL,
	GNotGoal float NOT NULL,
	CHECK(squadra1<>squadra2)
);

CREATE TABLE IF NOT EXISTS utenti(
	id BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	email VARCHAR(50) NOT NULL,
	nome VARCHAR(30) NOT NULL,
	cognome VARCHAR(30) NOT NULL,
	ruolo INT UNSIGNED NOT NULL,
	saldo INT UNSIGNED NOT NULL
);

CREATE TABLE IF NOT EXISTS giocate(
	partite_id BIGINT UNSIGNED NOT NULL REFERENCES partite(id),
	user_id BIGINT UNSIGNED NOT NULL REFERENCES utenti(id),
	scelta ENUM('G1', 'G2', 'GX', 'GGoal', 'GNotGoal') NOT NULL,
	importo INT UNSIGNED NOT NULL,
	PRIMARY KEY(partite_id, user_id)
);

INSERT INTO utenti VALUES
(null, 'paolo.foschini@gmail.com', 'Paolo', 'Fosc', 1, 30),
(null, 'francesco.foschini@gmail.com', 'Fra', 'Fos', 0, 0);

SELECT * FROM utenti;

INSERT INTO squadre VALUES
(null, 'Roma'),
(null, 'Lazio'),
(null, 'Inter'),
(null, 'Milan');

SELECT * FROM squadre;

INSERT INTO partite VALUES
(null, 1, 2, '2025-09-20', 1.01, 1.02, 10.3, 1.04, 1.05),
(null, 3, 4, '2025-09-23', 2.01, 3.02, 4.3, 5.04, 6.05);

SELECT * FROM partite;

INSERT INTO giocate VALUES
(1, 1, 'G1', 3),
(1, 2, 'GX', 5),
(2, 1, 'G2', 3),
(2, 2, 'GGoal', 5);

SELECT * FROM giocate;

SELECT partite.*, casa.nome AS casa, ospiti.nome AS ospiti FROM partite
	JOIN squadre casa ON partite.squadra1=casa.id
	JOIN squadre ospiti ON partite.squadra2=ospiti.id;

DELETE FROM giocate;