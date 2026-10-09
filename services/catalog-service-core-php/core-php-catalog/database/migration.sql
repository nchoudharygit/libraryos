-- Run this once against the catalog_core database to create the books table.
-- You can run it via: psql -h 127.0.0.1 -p 5433 -U postgres -d catalog_core -f migration.sql

CREATE TABLE IF NOT EXISTS books (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    isbn VARCHAR(20) NOT NULL UNIQUE,
    genre VARCHAR(100),
    copies_owned INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);
