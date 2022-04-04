<?php

namespace App\Services;

use App\Models\Account;
use Logging;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class AccountService
{
    /**
     * Return the list accounts specific for the current tenant_id
     *
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request, $tenantId)
    {
        if (is_null($tenantId)) {
            $tenantId = currentTenant('id');
        }

        $query = QueryBuilder::for(Account::where('tenant_id', $tenantId), $request)
            ->allowedFields(Account::$fields)
            ->allowedIncludes(Account::$includes)
            ->defaultSort('-id')
            ->allowedSorts(Account::$sorts);

        if (request()->has('filter') && isset(request()->filter['keyword'])) {
            $query->search(request()->filter['keyword']);
        }

        return $query;
    }

    /**
     * Return the list accounts with id and description
     * specific for the current tenant_id
     *
     * @return \Illuminate\Http\Response
     */
    public function optionList(Request $request, $tenantId)
    {
        return $this->list($request, $tenantId)->select("id", "description");
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return QueryBuilder::for(Account::where("id", $id))
            ->allowedFields(Account::$fields)
            ->allowedIncludes(Account::$includes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(array $data)
    {
        Logging::information('Create Account', $data, 1, 1);
        Account::create($data);
        return $this->list(request(), currentTenant('id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\Account $account
     */
    public function update(array $data, Account $account)
    {
        if (!is_null($account)) {
            $log['old_values'] = $account->getRawDBData();

            $account->update($data);
            $log['new_values'] = $account->getRawDBData();
            $log['changes'] = $account->getChanges();

            Logging::information('Update Account', $log, 1, 1);
        }
        return $this->list(request(), currentTenant('id'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Account $account
     */
    public function delete(Account $account)
    {
        Logging::information('Delete Account', $account, 1, 1);
        $account->delete();
        return $this->list(request(), currentTenant('id'));
    }

    /**
     * Count all accounts
     *
     * @return \Illuminate\Http\Response
     */
    public function count()
    {
        return Account::count();
    }
}
