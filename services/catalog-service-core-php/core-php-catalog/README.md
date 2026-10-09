# Catalog Service — Core PHP version

Same Books CRUD as `catalog-service` (Laravel), rebuilt in plain PHP — no framework —
to show the fundamentals underneath: manual routing, PDO, prepared statements, JSON responses.

## Folder structure

```
catalog-service-core-php/
├── public/
│   └── index.php        # front controller + router (entry point)
├── src/
│   ├── Database.php      # PDO connection (Singleton)
│   ├── Book.php           # Model — all DB queries for books
│   └── BookController.php # request handling + validation + JSON responses
├── config/
│   └── database.php       # DB credentials (host, port, dbname, user, password)
└── database/
    └── migration.sql      # run once to create the books table
```

## Setup

1. Create the database table (adjust host/port/user to match your Postgres):
```bash
psql -h 127.0.0.1 -p 5433 -U postgres -d catalog_core -f database/migration.sql
```

2. Update `config/database.php` with your actual DB password.

3. Start PHP's built-in server, pointing at the `public/` folder:
```bash
php -S localhost:8005 -t public
```

## Testing with Postman (or curl)

**Health check**
```
GET http://localhost:8005/api/v1/health
```

**List all books**
```
GET http://localhost:8005/api/v1/books
```

**Get one book**
```
GET http://localhost:8005/api/v1/books/1
```

**Create a book**
```
POST http://localhost:8005/api/v1/books
Content-Type: application/json

{
  "title": "Clean Code",
  "isbn": "9780132350884",
  "genre": "Software Engineering",
  "copies_owned": 3
}
```

**Update a book**
```
PUT http://localhost:8005/api/v1/books/1
Content-Type: application/json

{
  "title": "Clean Code (2nd Edition)",
  "isbn": "9780132350884",
  "genre": "Software Engineering",
  "copies_owned": 5
}
```

**Delete a book**
```
DELETE http://localhost:8005/api/v1/books/1
```

## Key concepts this project demonstrates (interview talking points)

- **Manual routing** — no framework magic, `$_SERVER['REQUEST_URI']` + regex matching
- **PDO with prepared statements** — every query uses named placeholders (`:id`, `:title`)
  to prevent SQL injection, never raw string concatenation
- **Singleton pattern** — one shared DB connection per request (`Database.php`)
- **Separation of concerns** — Model (`Book.php`) only knows about data,
  Controller (`BookController.php`) only knows about handling requests/responses
- **Consistent JSON responses** — every response goes through one `jsonResponse()` helper
  so the shape and status codes stay consistent
