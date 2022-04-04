<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\NoteApiRequest;
use App\Models\Note;
use Illuminate\Http\Request;
use App\Services\NoteService;

class NoteController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = new NoteService();
    }

    public function index($related, $type)
    {
        return $this->service->list($related, $type);
    }

    public function create($related, $type)
    {
        $data = request()->all();
        return $this->service->create($related, $type, $data);
    }

    public function update(Note $note, NoteApiRequest $request)
    {
        $datas = jsonRecode($request->all(Note::$fields));
        return $this->sendSingleResult(
            $this->service->update($datas, $note),
            'Note updated successfully.'
        );
    }
}
