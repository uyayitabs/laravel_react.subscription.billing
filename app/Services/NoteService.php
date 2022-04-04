<?php

namespace App\Services;

use Logging;
use App\Models\Note;
use App\Http\Resources\NoteResource;

class NoteService
{
    public function list($related, $type)
    {
        $query = Note::where([
            ['type', '=', $type],
            ['related_id', '=', $related]
        ])->orderBy('id', 'DESC');
        $limit = request()->query('offset', 10);
        return new NoteResource($query->paginate($limit));
    }

    public function create($related, $type, $data, $user_id = null)
    {
        $data['related_id'] = $related;
        $data['type'] = $type;
        $data['user_id'] = !$user_id ? request()->user()->id : $user_id;
        Logging::information('Create Note', $data, 1, 1);
        $note = Note::create($data);
        return $note;
    }

    public function update($data, $note)
    {
        $log['old_values'] = $note->getRawDBData();

        $note->update($data);
        $log['new_values'] = $note->getRawDBData();
        $log['changes'] = $note->getChanges();

        Logging::information('Update Note', $log, 1, 1);
        return Note::where('id', $note->id);
    }
}
