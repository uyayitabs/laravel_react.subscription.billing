<?php

use Illuminate\Database\Seeder;

use App\Relation;
use App\Note;

class InformationToNotes extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $relations = Relation::whereNotNull('info')->get();
        foreach ($relations as $relation) {
            Note::create([
                'type' => 'relations',
                'related_id' => $relation->id,
                'text' => $relation->info
            ]);
        }
    }
}
