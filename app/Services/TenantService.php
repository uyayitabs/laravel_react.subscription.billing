<?php

namespace App\Services;

use App\Models\BillingRun;
use App\Models\SalesInvoice;
use App\Models\Tenant;
use App\Repositories\TenantRepository;
use App\Services\VatCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Http\Resources\VatCodeResource;
use App\Mail\PainDirectDebitMail;
use App\Models\User;
use Carbon\Carbon;
use DOMDocument;
use Illuminate\Support\Facades\Mail;
use Spatie\ArrayToXml\ArrayToXml;

class TenantService
{
    protected $repository;
    protected $vatCodeService;

    public function __construct()
    {
        $this->repository = new TenantRepository();
        $this->vatCodeService = new VatCodeService();
    }

    public function list(Request $request)
    {
        return $this->repository->all($request);
    }

    public function create($data)
    {
        return $this->repository->create($data);
    }

    public function update()
    {
    }

    public function count()
    {
        return $this->repository->count();
    }

    public function vatCodes(Tenant $tenant)
    {
        return new VatCodeResource($this->vatCodeService->list($tenant->id)->paginate());
    }
}
