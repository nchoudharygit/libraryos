<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            ['author_id' => 1, 'title' => 'Five Point Someone','isbn' => '978-0143105965'],
            ['author_id' => 1, 'title' => '2 States','isbn' => '978-0143105966'],
            ['author_id' => 2, 'title' => 'The God of Small Things','isbn' => '978-0143105967'],
            ['author_id' => 3, 'title' => '1984','isbn' => '978-0143105968'],
            ['author_id' => 3, 'title' => 'Animal Farm','isbn' => '978-0143105969'],
            ['author_id' => 4, 'title' => 'Sapiens','isbn' => '978-0143105970'],
            ];
        foreach ($books as $book) {
            \App\Models\Book::create($book);
        }
    }
}
