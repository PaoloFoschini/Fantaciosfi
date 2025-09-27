DROP TABLE IF EXISTS bets;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS matches;
DROP TABLE IF EXISTS teams;

CREATE TABLE IF NOT EXISTS teams(
	id BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	name VARCHAR(20) NOT NULL
);

CREATE TABLE IF NOT EXISTS matches(
	id BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	team1 BIGINT UNSIGNED NOT NULL REFERENCES teams(id),
	team2 BIGINT UNSIGNED NOT NULL REFERENCES teams(id),
	match_start_date DATETIME NOT NULL,
	league VARCHAR(20) NOT NULL,
	GW1 float NOT NULL,
	GX float NOT NULL,
	GW2 float NOT NULL,
	GG float NOT NULL,
	GNG float NOT NULL,
	result VARCHAR(10),
	isFinished BOOLEAN NOT NULL DEFAULT false,
	CHECK(team1<>team2)
);

CREATE TABLE IF NOT EXISTS users(
	id BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
	email VARCHAR(50) NOT NULL,
	teamname VARCHAR(30) NOT NULL,
	role INT UNSIGNED NOT NULL,
	balance INT UNSIGNED NOT NULL
);

CREATE TABLE IF NOT EXISTS bets(
	match_id BIGINT UNSIGNED NOT NULL REFERENCES matches(id),
	user_id BIGINT UNSIGNED NOT NULL REFERENCES users(id),
	choice ENUM('W1', 'X', 'W2', 'G', 'NG') NOT NULL,
	amount INT UNSIGNED NOT NULL,
	paid BOOLEAN NOT NULL DEFAULT false,
	payout INT UNSIGNED,
	PRIMARY KEY(match_id, user_id)
);

INSERT INTO users VALUES
(null, 'pol.foschini@gmail.com', 'AdminTeam', 1, 30),
(null, 'franci.foschini@gmail.com', 'AdminTeam', 1, 10),
(null, 'rob.foschini@gmail.com', 'AdminTeam', 1, 10),

(null, 'paolofoschini04@gmail.com', 'Cavia', 1, 30),

(null, 'nicola.mariani09@gmail.com', 'ScoReggiana', 0, 0),
(null, 'pkpiazza99@gmail.com', 'FC Flaconte', 0, 0),
(null, 'davide99perrone@gmail.com', 'QQQ', 0, 0),
(null, 'sebastiano.martelli@gmail.com', 'Sporting Saragozza 157', 0, 0),
(null, 'giovannirotondi1999@gmail.com', 'Big brazzers', 0, 0),	
(null, 'filippo.tarroni@gmail.com', 'FCentrocampo', 0, 0),
(null, '@gmail.com', 'PCM compl8', 0, 0),
(null, 'davidecolaci.dc@gmail.com', 'AC Ciughina', 0, 0),
(null, 'giulio636@gmail.com', 'mazzo e Giulio', 0, 0),
(null, 'andreamarzucco99@gmail.com', 'OktoberPasta FC', 0, 0),

(null, 'piroddienrico99@gmail.com', 'Pirona Futbal Club', 0, 0),
(null, 'gialloguerra@gmail.com', 'Ginlypuff Fc', 0, 0),
(null, 'fabiomengozzi15@gmail.com', 'DVX Revive S.S.', 0, 0),
(null, 'cerryale99@gmail.com', 'Tunde Team', 0, 0),
(null, 'gabripoli99.gp@gmail.com', 'Hospice Villa Adalgisa', 0, 0),
(null, 'manuelmenico@gmail.com', 'parco delle cascine', 0, 0),
(null, 'davgui99@gmail.com', 'Guido Levercusen', 0, 0),
(null, 'parideo99@gmail.com', 'Kim Jong United', 0, 0),
(null, 'francegatta1@gmail.com', 'GRANCHI AVATORI', 0, 0),
(null, 'mattigale8@gmail.com', 'Hulltra Duce', 0, 0),

(null, 'vernocchiluca@gmail.com', 'RiVern Plate', 0, 0),
(null, 'marcobrighi18@gmail.com', 'BeTisBrighi', 0, 0),
(null, 'lorenzominghetti1999@gmail.com', 'Hajduk Spatalo', 0, 0),
(null, 'steve.baru00@gmail.com', 'Hapoel Baru-Sheva', 0, 0),
(null, 'marcogalli98@gmail.com', 'PARIS SAINT GALLAIN', 0, 0),
(null, 'lucadari98@gmail.com', 'fAC CETTA NERA', 0, 0),
(null, 'simonetonini97@gmail.com', 'Osatuna', 0, 0),
(null, 'vernocchiandre@gmail.com', 'Virtus Vernona', 0, 0),
(null, 'ste141298@gmail.com', 'LosDoggosSC', 0, 0),
(null, '@gmail.com', 'Balla coi Lupi', 0, 0);

SELECT * FROM users;

INSERT INTO teams VALUES
(null, 'Roma'),
(null, 'Lazio'),
(null, 'Inter'),
(null, 'Milan');

SELECT * FROM teams;

INSERT INTO matches VALUES
(null, 1, 2, '2025-09-20 20:45:00', 'Serie A', 1.01, 1.02, 10.3, 1.04, 1.05, null, false),
(null, 3, 4, '2025-09-23 18:30:00', 'Bundesliga', 2.01, 3.02, 4.3, 5.04, 6.05, null, false);

SELECT * FROM matches;

/**INSERT INTO bets VALUES
(1, 1, 'W1', 3, false, 3.03),
(1, 2, 'X', 5, false, null),
(2, 1, 'W2', 3, true, null),
(2, 2, 'G', 5, true, null);
*/
SELECT * FROM bets;

SELECT matches.*, home.name AS home, away.name AS away FROM matches
	JOIN teams home ON matches.team1=home.id
	JOIN teams away ON matches.team2=away.id;

DELETE FROM bets;

GRANT select, insert, update, delete ON fantaciosfi.* TO 'www-data'@localhost;
