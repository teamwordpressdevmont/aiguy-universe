<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ToolReview;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ToolReview::insert([
            [
                'tool_id'  => 1,
                'user_id'  => 37,
                'rating'   => 5,
                'review'   => 'Excellent tool, very useful!',
                'approved' => 1,
            ],
            [
                'tool_id'  => 1,
                'user_id'  => 37,
                'rating'   => 4,
                'review'   => 'Great tool, but can be improved.',
                'approved' => 1,
            ],
            [
                'tool_id'  => 1,
                'user_id'  => 37,
                'rating'   => 3,
                'review'   => 'Average experience, needs more features.',
                'approved' => 1,
            ],
        ]);
    }
}