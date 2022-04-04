<?php

namespace App\Services;

use Logging;
use App\Models\PdfTemplate;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class PdfTemplateService
{
    public function list(Request $request, $tenantId)
    {
        if (is_null($tenantId)) {
            $tenantId = currentTenant('id');
        }

        $query = PdfTemplate::where('tenant_id', $tenantId);

        return QueryBuilder::for($query, $request)
                    ->allowedFields(PdfTemplate::$fields)
                    ->allowedIncludes(PdfTemplate::$includes)
                    ->defaultSort('-id')
                    ->allowedSorts(PdfTemplate::$sorts);
    }

    public function show($id)
    {
        return QueryBuilder::for(PdfTemplate::where("id", $id))
                 ->allowedFields(PdfTemplate::$fields)
                 ->allowedIncludes(PdfTemplate::$includes);
    }

    public function create(array $data)
    {
        // PdfTemplate::where('tenant_id', $data["tenant_id"])->update([
        //     "version" => "closed",
        //     "type" => $data["type"]
        // ]);
        PdfTemplate::create($data);
        return $this->list(request(), currentTenant('id'));
    }

    public function update(array $data, PdfTemplate $pdfTemplate)
    {
        if (!is_null($pdfTemplate)) {
            $log['old_values'] = $pdfTemplate->getRawDBData();

            $pdfTemplate->update($data);

            $log['new_values'] = $pdfTemplate->getRawDBData();
            $log['changes'] = $pdfTemplate->getChanges();

            Logging::information('Update PDF Template', $log, 1, 1);
        }
        return $this->list(request(), currentTenant('id'));
    }

    public function delete(PdfTemplate $pdfTemplate)
    {
        $pdfTemplate->delete();
        return $this->list(request(), currentTenant('id'));
    }

    public function count()
    {
        return PdfTemplate::count();
    }
}
