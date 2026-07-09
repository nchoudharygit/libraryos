<?php
namespace Database\Seeders;
use App\Models\Author;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {  
        $authors = [
            ['name' => 'Chetan Bhagat','nationality' => 'Indian'],
            ['name' => 'Arundhati Roy','nationality' => 'Indian'],
            ['name' => 'George Orwell','nationality' => 'British'],
            ['name' => 'Yuval Noah Harari','nationality' => 'Israeli'],
        ];

        foreach ($authors as $author) {
            Author::create($author);
        }
    }
}
            