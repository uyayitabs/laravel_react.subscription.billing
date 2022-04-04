<?php

namespace App\Services;

use Logging;
use App\Http\Resources\BaseResourceCollection;
use App\Http\Resources\OrderResource;
use App\Mail\OrderErrorMail;
use App\Mail\OrderSuccessMail;
use App\Models\Order;
use App\Models\StatusType;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\QueryBuilder;

class OrderService
{
    protected $order;
    protected $searchableFields = [
        'date',
        'customer',
        'address',
        'status'
    ];

    /**
     * List orders
     *
     * @return BaseResourceCollection
     */
    public function list()
    {

        $query = Order::select();

        // search filter
        $query = $this->handleSearchFilters(
            $query,
            request()->query("filter", [])
        );

        $statusType = StatusType::where('type', 'order')
            ->first();
        $status = Status::where('status_type_id', $statusType->id)
            ->get()
            ->toArray();

        // sorting
        $query = $this->handleSorting(
            $query,
            request()->query('sort', '-created_at')
        );

        // pagination (page & limit)
        $limit = request()->query('offset', 50);
        $query = $query->paginate($limit);

        // JsonResource implementation
        $query->transform(function (Order $order) use ($status) {
            return (new OrderResource(
                $order,
                $status,
                'Order retrieved successfully.',
                true,
                true
            ));
        });

        return new BaseResourceCollection($query);
    }

    /**
     * Handle search filters
     *
     * @param mixed $modelQuery
     * @param mixed $searchFilter
     * @return mixed
     */
    public function handleSearchFilters($modelQuery, $searchFilter)
    {
        foreach ($searchFilter as $key => $value) {
            if (in_array($key, $this->searchableFields)) {
                if ($key == 'date' && !empty($value)) {
                    $modelQuery->where('date', Carbon::parse($value)->format("Y-m-d"));
                } elseif ($key == 'status' && !empty($value)) {
                    $statusService = new StatusService();
                    $statusId = $statusService->getStatusId('order', $value);

                    $modelQuery->where('status_id', $statusId);
                } else {
                    $customerFields = ['first', 'middle', 'last'];
                    $addressFields = ['street', 'postal_code', 'city'];

                    if ($key == 'customer' && !empty($value)) {
                        $modelQuery->where(function ($query2) use ($value) {
                            $query2->whereRaw("LOWER(JSON_EXTRACT(`data`, '$.customer.name.first')) like LOWER(JSON_QUOTE('%{$value}%'))");
                            $query2->orWhereRaw("LOWER(JSON_EXTRACT(`data`, '$.customer.name.middle')) like LOWER(JSON_QUOTE('%{$value}%'))");
                            $query2->orWhereRaw("LOWER(JSON_EXTRACT(`data`, '$.customer.name.last')) like LOWER(JSON_QUOTE('%{$value}%'))");
                        });
                    }
                    if ($key == 'address' && !empty($value)) {
                        $modelQuery->where(function ($query2) use ($value) {
                            $query2->whereRaw("LOWER(JSON_EXTRACT(`data`, '$.address.street')) like LOWER(JSON_QUOTE('%{$value}%'))");
                            $query2->orWhereRaw("LOWER(JSON_EXTRACT(`data`, '$.address.postal_code')) like LOWER(JSON_QUOTE('%{$value}%'))");
                            $query2->orWhereRaw("LOWER(JSON_EXTRACT(`data`, '$.address.city')) like LOWER(JSON_QUOTE('%{$value}%'))");
                        });
                    }
                }
            }
        }
        return $modelQuery;
    }

    /**
     * Handle sorting
     *
     * @param mixed $modelQuery
     * @param mixed $sortFilter
     * @return mixed
     */
    public function handleSorting($modelQuery, $sortFilter)
    {
        if ($sortFilter) {
            $sequence = Str::contains($sortFilter, '-') ? 'DESC' : 'ASC';
            $columnName = str_replace('-', '', $sortFilter);
            $modelQuery->orderBy($columnName, $sequence);
        }
        return $modelQuery;
    }

    /**
     * Create order
     *
     * @param mixed $data
     * @return \App\Order;
     */
    public function create($data)
    {
        $statusService = new StatusService();
        $newStatusId = $statusService->getStatusId('order', 'new');

        $order = Order::create([
            'date' => now()->format('Y-m-d'),
            'source' => $data['source'],
            'data' => $data,
            'status_id' => $newStatusId,
        ]);
        return $order;
    }

    /**
     * Send success email
     *
     * @param mixed $orderId
     * @param mixed $tenantId
     */
    public function sendSuccessEmail($orderId, $tenantId)
    {
        $order = Order::find($orderId);
        $extras = "";
        foreach ($order->data["product"]["tv"]["extra_packages"] as $extra) {
            $extras .= $extra['name'] . ",";
        }

        // Send order success email to the contact.email
        $recipientEmail = setEmailAddress($order->data['contact']['email']);
        Mail::to($recipientEmail)
            ->send((new OrderSuccessMail(
                [
                    "order" => $order->data,
                    "extras" => rtrim($extras, ',')
                ],
                $tenantId
            )));
    }

    /**
     * Send error email
     *
     * @param mixed $inputParams
     */
    public function sendErrorEmail($inputParams)
    {
        // Send mail to Marisa noting about the error
        Mail::to(config('app.error_mail_recipient'))
            ->send((new OrderErrorMail([
                "error_details" => json_encode($inputParams, JSON_PRETTY_PRINT),
            ])));
    }


    /**
     * Update order
     *
     * @return \App\Order;
     */
    public function update(Order $order)
    {
        $attributes = request(Order::$fields);
        $attributes['updated_by'] = request()->user()->id;

        $log['old_values'] = $order->getRawDBData();

        $order->update($attributes);

        $log['new_values'] = $order->getRawDBData();
        $log['changes'] = $order->getChanges();

        Logging::information('Update Orders', $log, 1, 1);
        return $order;
    }

    /**
     * Return Order data
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return QueryBuilder::for(Order::where('id', $id))
            ->allowedFields(Order::$fields)
            ->allowedIncludes(Order::$scopes);
    }
}
