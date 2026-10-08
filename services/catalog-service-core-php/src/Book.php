<?php

class Book
{
    public static function getAllBooks(): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->query('SELECT * FROM books ORDER BY id DESC');
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $books;
    }

    public static function getBookById(int $id): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM books WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);
        return $book ?: null;
    }

    public static function createBook(array $data): bool
    {
        $pdo = Database::connect();
       $stmt = $pdo->prepare(
            'INSERT INTO books (title, isbn, genre, copies_owned)
             VALUES (:title, :isbn, :genre, :copies_owned)
            '
        );
        return $stmt->execute([
            'title' => $data['title'],
            'isbn' => $data['isbn'],
            'genre' => $data['genre'] ?? null,
            'copies_owned' => $data['copies_owned'] ?? 1,
        ]);
    }

    public static function updateBookById(int $id, array $data): bool
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            'UPDATE books
            SET 
            title = :title, 
            isbn = :isbn, 
            genre = :genre, 
            copies_owned = :copies_owned, 
            updated_at = NOW()
            WHERE
            id = :id
            '
        );
        $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'isbn' => $data['isbn'],
            'genre' => $data['genre'] ?? null,
            'copies_owned' => $data['copies_owned'] ?? 1,
        ]);
        return $stmt->rowCount() > 0;

    }

    public static function deleteBookById(int $id): bool
    {
        $pdo = Database::connect();   
        $stmt = $pdo->prepare('DELETE FROM books WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() > 0;
    }

}
