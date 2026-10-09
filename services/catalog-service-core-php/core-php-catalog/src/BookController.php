<?php

class BookController
{
    // GET /books
    public function index(): void
    {
        $books = Book::all();
        $this->jsonResponse($books, 200);
    }

    // GET /books/{id}
    public function show(int $id): void
    {
        $book = Book::find($id);

        if ($book === null) {
            $this->jsonResponse(['error' => 'Book not found'], 404);
            return;
        }

        $this->jsonResponse($book, 200);
    }

    // POST /books
    public function store(): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];

        $errors = $this->validate($input);
        if (!empty($errors)) {
            $this->jsonResponse(['errors' => $errors], 422);
            return;
        }

        $book = Book::create($input);
        $this->jsonResponse($book, 201);
    }

    // PUT/PATCH /books/{id}
    public function update(int $id): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];

        $errors = $this->validate($input);
        if (!empty($errors)) {
            $this->jsonResponse(['errors' => $errors], 422);
            return;
        }

        $book = Book::update($id, $input);

        if ($book === null) {
            $this->jsonResponse(['error' => 'Book not found'], 404);
            return;
        }

        $this->jsonResponse($book, 200);
    }

    // DELETE /books/{id}
    public function destroy(int $id): void
    {
        $deleted = Book::delete($id);

        if (!$deleted) {
            $this->jsonResponse(['error' => 'Book not found'], 404);
            return;
        }

        $this->jsonResponse(['message' => 'Book deleted'], 200);
    }

    // Basic validation - keeps store/update clean
    private function validate(array $input): array
    {
        $errors = [];

        if (empty($input['title'])) {
            $errors[] = 'title is required';
        }

        if (empty($input['isbn'])) {
            $errors[] = 'isbn is required';
        }

        return $errors;
    }

    // Helper so every response is consistent JSON with the right status code
    private function jsonResponse($data, int $statusCode): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
