PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS user (
                                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                                    username TEXT NOT NULL,
                                    email TEXT NOT NULL UNIQUE,
                                    password_hash TEXT NOT NULL,
                                    role TEXT NOT NULL DEFAULT 'user' CHECK (role IN ('admin', 'user')),
                                    created_at TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS games (
                                     id INTEGER PRIMARY KEY AUTOINCREMENT,
                                     name TEXT NOT NULL,
                                     type TEXT NOT NULL,
                                     description TEXT,
                                     image TEXT,
                                     price REAL NOT NULL DEFAULT 0,
                                     created_at TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS user_games (
                                          id INTEGER PRIMARY KEY AUTOINCREMENT,
                                          user_id INTEGER NOT NULL,
                                          game_id INTEGER NOT NULL,
                                          added_at TEXT NOT NULL DEFAULT (datetime('now')),
                                          playtime_hours INTEGER NOT NULL DEFAULT 0,
                                          FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
                                          FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS achievements (
                                            id INTEGER PRIMARY KEY AUTOINCREMENT,
                                            game_id INTEGER NOT NULL,
                                            name TEXT NOT NULL,
                                            description TEXT,
                                            rarity TEXT NOT NULL DEFAULT 'common' CHECK (rarity IN ('common', 'uncommon', 'rare', 'epic', 'legendary')),
                                            FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS user_achievements (
                                                 id INTEGER PRIMARY KEY AUTOINCREMENT,
                                                 user_id INTEGER NOT NULL,
                                                 achievement_id INTEGER NOT NULL,
                                                 unlocked_at TEXT NOT NULL DEFAULT (datetime('now')),
                                                 FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE,
                                                 FOREIGN KEY (achievement_id) REFERENCES achievements(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS levels (
                                      id INTEGER PRIMARY KEY AUTOINCREMENT,
                                      game_id INTEGER NOT NULL,
                                      name TEXT NOT NULL,
                                      difficulty TEXT NOT NULL DEFAULT 'medium' CHECK (difficulty IN ('easy', 'medium', 'hard', 'extreme')),
    description TEXT,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE
    );