<?php

use Illuminate\Database\Seeder;
use App\Eloquent\Book;
use App\Eloquent\Category;

class BookTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Book::class, 26)->create()->each(function ($book) {
            $book->categories()->saveMany(
                Category::all()->random(1)
            );
            DB::table('media')->insert([
                'path' => rand(10, 20) . '.jpg',
                'priority' => rand(0, 3),
                'target_type' => 'App\Eloquent\Book',
                'target_id' => $book->id,
            ]);
        });
    }
}
