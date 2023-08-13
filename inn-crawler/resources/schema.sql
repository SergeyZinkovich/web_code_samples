CREATE TABLE requests (
    id SERIAL PRIMARY KEY,
    request_id TEXT,
    created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    first_name TEXT NOT NULL,
    second_name TEXT,
    last_name TEXT NOT NULL,
    birthday TIMESTAMP NOT NULL,
    passport_number TEXT NOT NULL,
    passport_date TIMESTAMP,
    inn TEXT,
    entity_id INTEGER,
    status SMALLINT DEFAULT 0
);