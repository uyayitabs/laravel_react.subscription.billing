<?php

namespace App\Services;

use App\Models\Group;
use Logging;
use App\Models\Role;
use Spatie\QueryBuilder\QueryBuilder;

class GroupService
{
    public function list($tenantId = null)
    {
        if (is_null($tenantId)) {
            $tenantId = currentTenant('id');
        }

        $query =  QueryBuilder::for(Group::where('tenant_id', $tenantId))
            ->allowedFields(Group::$fields)
            ->allowedIncludes(Group::$includes)
            ->defaultSort('-id')
            ->allowedSorts([
                'name',
                'description'
            ]);

        if (request()->has('filter') && isset(request()->filter['keyword'])) {
            $query->search(request()->filter['keyword']);
        }

        return $query;
    }

    public function create($data)
    {
        Logging::information('Create Group', $data, 1, 1);
        $group = Group::create($data['group']);
        $roles = $data['roles'];

        foreach ($roles as $slug => $val) {
            $role = Role::slug($slug)->first();
            $write = $val['value'] == '11' ? 1 : 0;
            $read = $val['value'] == '11' || $val['value'] == '01' ? 1 : 0;

            $group
                ->groupRoles()
                ->create([
                    'role_id' => $role->id,
                    'write' => $write,
                    'read' => $read,
                ]);
        }
        return $group;
    }

    public function update($group, $data)
    {
        $roles = $data['roles'];

        $log['old_values'] = $group->getRawDBData();

        $group->update($data['group']);
        $log['new_values'] = $group->getRawDBData();
        $log['changes'] = $group->getChanges();

        Logging::information('Update Group', $log, 1, 1);

        foreach ($roles as $slug => $val) {
            $role = Role::slug($slug)->first();
            $groupRole = $group
                ->groupRoles()
                ->where('role_id', $role->id)
                ->first();

            $write = $val['value'] == '11' ? 1 : 0;
            $read = $val['value'] == '11' || $val['value'] == '01' ? 1 : 0;

            $groupRoleParam = [
                'write' => $write,
                'read' => $read,
            ];

            if ($groupRole) {
                $groupRole->update($groupRoleParam);
            } else {
                $groupRoleParam['role_id'] = $role->id;
                $group->groupRoles()->create($groupRoleParam);
            }
        }
    }
}
