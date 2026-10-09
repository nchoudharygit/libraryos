<?php

class Book
{
    // Get all books
    public static function all(): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->query('SELECT * FROM books ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    // Get one book by id
    public static function find(int $id): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM books WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $book = $stmt->fetch();
        return $book ?: null;
    }

    // Create a new book, return the created row
    public static function create(array $data): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            'INSERT INTO books (title, isbn, genre, copies_owned)
             VALUES (:title, :isbn, :genre, :copies_owned)
             RETURNING *'
        );
        $stmt->execute([
            'title' => $data['title'],
            'isbn' => $data['isbn'],
            'genre' => $data['genre'] ?? null,
            'copies_owned' => $data['copies_owned'] ?? 1,
        ]);
        return $stmt->fetch();
    }

    // Update an existing book, return the updated row (or null if not found)
    public static function update(int $id, array $data): ?array
    {
        if (self::find($id) === null) {
            return null;
        }

        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            'UPDATE books
             SET title = :title, isbn = :isbn, genre = :genre, copies_owned = :copies_owned, updated_at = NOW()
             WHERE id = :id
             RETURNING *'
        );
        $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'isbn' => $data['isbn'],
            'genre' => $data['genre'] ?? null,
            'copies_owned' => $data['copies_owned'] ?? 1,
        ]);
        return $stmt->fetch();
    }

    // Delete a book, return true if a row was actually deleted
    public static function delete(int $id): bool
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('DELETE FROM books WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
