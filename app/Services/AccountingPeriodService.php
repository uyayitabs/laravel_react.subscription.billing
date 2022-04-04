<?php

namespace App\Services;

use App\Models\AccountingPeriod;
use Logging;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class AccountingPeriodService
{
    public function list(Request $request, $tenantId, $fiscalYearId)
    {
        $params = [];
        if (is_null($tenantId)) {
            $tenantId = currentTenant('id');
        }
        $params[] = ['tenant_id', "=", $tenantId];
        if (!is_null($fiscalYearId)) {
            $params[] = ['fiscal_year_id', '=', $fiscalYearId];
        }

        $query = AccountingPeriod::where($params);

        return QueryBuilder::for($query, $request)
                    ->allowedFields(AccountingPeriod::$fields)
                    ->allowedIncludes(AccountingPeriod::$includes)
                    ->defaultSort('-id')
                    ->allowedSorts(AccountingPeriod::$sorts);
    }

    public function create(array $data)
    {
        AccountingPeriod::create($data);
        return $this->list(request(), currentTenant('id'), null);
    }


    public function show($id)
    {
        return QueryBuilder::for(AccountingPeriod::where("id", $id))
                 ->allowedFields(AccountingPeriod::$fields)
                 ->allowedIncludes(AccountingPeriod::$includes);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Models\AccountingPeriod $accountingPeriod
     * @return \Illuminate\Http\Response
     */
    public function update(array $data, AccountingPeriod $accountingPeriod)
    {
        if (!is_null($accountingPeriod)) {
            $log['old_values'] = $accountingPeriod->getRawDBData();

            $accountingPeriod->update($data);
            $log['new_values'] = $accountingPeriod->getRawDBData();
            $log['changes'] = $accountingPeriod->getChanges();

            Logging::information('Update Accounting Period', $log, 1, 1);
        }
        return $this->list(request(), currentTenant('id'), null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AccountingPeriod $accountingPeriod
     */
    public function delete(AccountingPeriod $accountingPeriod)
    {
        $accountingPeriod->delete();
        return $this->list(request(), currentTenant('id'), null);
    }

    /**
     * count all accounting period
     *
     * @param  \App\Models\AccountingPeriod $accountingPeriod
     */
    public function count()
    {
        return AccountingPeriod::count();
    }
}
