<?php

namespace App\Services;

use App\Models\Serial;
use App\Models\SubscriptionLine;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\DB;

class SerialService
{
    /**
     * Display a listing serials
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        return QueryBuilder::for(Serial::class, request())
            ->allowedFields(Serial::$fields)
            ->allowedFilters(Serial::$fields)
            ->defaultSort('-id')
            ->allowedSorts(Serial::$fields);
    }

    /**
     * Store a newly created stock
     *
     * @return \Illuminate\Http\Response
     */
    public function create(array $data)
    {
        $serial = Serial::create($data);
        Logging::information('Create Serial', $data, 1, 1);

        return QueryBuilder::for(Serial::where('id', $serial->id))
            ->allowedFields(Serial::$fields);
    }

    /**
     * Display the specified stock
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return QueryBuilder::for(Serial::where('id', $id))
            ->allowedFields(Serial::$fields);
    }

    /**
     * Update the specified stock
     *
     * @param \App\Models\Serial $serial
     *
     * @return \Illuminate\Http\Response
     */
    public function update(array $data, Serial $serial)
    {
        $log['old_values'] = $serial->getRawDBData();

        $serial->update($data);
        $log['new_values'] = $serial->getRawDBData();
        $log['changes'] = $serial->getChanges();

        Logging::information('Update Serial', $log, 1, 1);

        return QueryBuilder::for(Serial::where('id', $serial->id))
            ->allowedFields(Serial::$fields);
    }

    /**
     * Check serial already exist
     * @param string $serial
     * @param int $slid
     * @return bool
     */
    public function isTaken($serial, $slid)
    {
        $subscriptionLine = SubscriptionLine::where('serial', $serial)->first();
        return $subscriptionLine && $slid != $subscriptionLine->id;
    }

    /**
     * Check serial already exist
     * @param string $serial
     * @return bool
     */
    public function isExist($serial)
    {
        return Serial::where('serial', $serial)->exists();
    }

    /**
     * Get serial
     * @param string $serial
     * @return bool
     */
    public function detail($serial)
    {
        return Serial::where('serial', $serial)->first();
    }

    /**
     * Get serial
     * @param string $serial
     */
    public function remove($serial)
    {
        DB::table('serials')->where('serial', $serial->serial)->delete();
    }
}
