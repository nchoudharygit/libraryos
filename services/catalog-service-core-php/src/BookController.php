<?php


class BookController {

    public static function index () 
    {
        $books = Book::getAllBooks();
        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode($books);
    }

    public static function show(int $id)
    {

        
    }

    public static function store()
    {

    }

    public static function update(int $id)
    {

    }

    public static function destroy(int $id)
    {
        
    }

}