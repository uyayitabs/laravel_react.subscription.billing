<?php

namespace App\Services;

use App\Http\Resources\RelationPersonResource;
use Logging;
use App\Http\Resources\PersonResource;
use App\Models\Person;
use App\Models\Relation;
use App\Models\RelationsPerson;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PersonService
{
    /**
     * Display a listing of persons
     *
     * @param int $tenantId
     * @return Builder
     */
    public function list(int $tenantId): Builder
    {
        return \Querying::for(Person::class)
            ->enableFillableSelect()
            ->setFilter(request()->get('filter'))
            ->setSortable(request()->get('sort'))
            ->setSelectables(request()->get('select'))
            ->setSearch(request()->get('search'))
            ->defaultSort('-id')
            ->make()
            ->getQuery()
            ->whereHas('relations', function (Builder $query) use ($tenantId) {
                $query->where('tenant_id', $tenantId);
            });
    }

    public function handleSearchFilters($modelQuery, $searchFilter)
    {
        if ($searchFilter && array_key_exists('keyword', $searchFilter)) {
            $value = $searchFilter['keyword'];
            $modelQuery->search($value);
        }

        return $modelQuery;
    }

    public function handleSorting($modelQuery, $sortFilter)
    {
        if ($sortFilter) {
            $sequence = Str::contains($sortFilter, '-') ? 'DESC' : 'ASC';
            $columnName = str_replace('-', '', $sortFilter);
            return $modelQuery->orderBy($columnName, $sequence);
        }
        return $modelQuery;
    }

    public function savePerson(array $data)
    {
        $attributes = filterArrayByKeys($data, Person::$fields);
        $person = new Person($attributes);

        Logging::information('Create Person', $attributes, 1, 1);

        if ($person->save()) {
            $relationsPersonAttributes = [
                'relation_id' => $data['relation_id'],
                'person_id' => $person->id,
                'status' => $data['status'],
                'primary' => (key_exists('primary', $data) && $data['primary']),
                'person_type_id' => $data['person_type_id']
            ];

            $rp = new RelationsPerson($relationsPersonAttributes);
            $rp->save();

            if ($rp->primary) {
                $this->switchPrimaryPerson($rp, $data['relation_id']);
            }
            return $person;
        } else {
            return null;
        }
    }

    /**
     * Store a newly created person
     */
    public function create(array $data)
    {
        $person = $this->savePerson($data);

        if ($person) {
            return [
                'success' => true,
                'data' => $this->show($person->id)
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Could not create a Person with the given data'
            ];
        }
    }

    /**
     * Link the existing person to a relation
     */
    public function linkperson(array $data)
    {
        $relationsPerson = new RelationsPerson($data);
        $relationsPerson->primary = false;
        $relationsPerson->save();

        Logging::information('Link Existing Person to a Relation', $relationsPerson, 1, 1);
        return $this->show($data['person_id'], $data['relation_id']);
    }

    public function show($person_id, $message = '', $code = true)
    {
        return new PersonResource(
            Person::find($person_id),
            $message,
            $code,
            true
        );
    }

    //A person is always updated in relation to a relation
    public function update(array $data, Person $person)
    {
        $log['old_values'] = $person->getRawDBData();

        $personAttributes = [
            'gender' => $data['gender'],
            'title' => $data['title'],
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'mobile' => $data['mobile'],
            'language' => $data['language'],
            'linkedin' => $data['linkedin'],
            'facebook' => $data['facebook'],
            'birthdate' => $data['birthdate']
        ];

        $person->update($personAttributes);
        $log['new_values'] = $person->getRawDBData();
        $log['changes'] = $person->getChanges();

        Logging::information('Update Person', $log, 1, 1);
        return [
            'success' => true,
            'data' => $this->show($person->id)
        ];
    }

    public function delete(Person $person)
    {
        Logging::information('Delete Person', $person, 1, 1);
        $person->delete();
        return [
            'success' => true,
            'data' => null,
            'message' => 'Person deleted successfully'
        ];
    }

    public function count()
    {
        $relations = Relation::where('tenant_id', currentTenant('id'))
            ->withCount('persons')
            ->get();

        $result = $relations->sum(function ($relation) {
            return $relation->persons_count;
        });

        return $result;
    }

    /**
     * check if email already exists on either user or person table
     */
    public function checkemail()
    {
        $data = Person::select(
            'relations.tenant_id',
            'relation_id',
            'person_id',
            'persons.email',
            'persons.first_name',
            'persons.middle_name',
            'persons.last_name',
            'relations.customer_number',
            'relations.company_name'
        )
            ->distinct()
            ->join('relations_persons', 'relations_persons.person_id', '=', 'persons.id')
            ->join('relations', 'relations.id', '=', 'relations_persons.relation_id')
            ->where('relations.tenant_id', currentTenant('id'))
            ->where('persons.email', request('email'))
            ->get();

        foreach ($data as $d) {
            if ($d->relation_id == request('relation_id')) {
                return 'duplicate';
            }
        }

        $persons = $data->map(function ($person) {
            $pcs = [];
            $pcs[] = $person->customer_number;
            if ($person->company_name) {
                $pcs[] = $person->company_name;
            }
            $pcs[] = $person->full_name;

            return [
                'relation_id' => $person->relation_id,
                'person_id' => $person->person_id,
                'full_name' => implode(' | ', $pcs)
            ];
        });

        return $persons;
    }

    /**
     * This function handles the logic when a new person gets the primary status
     * or when a person switches their primary status
     *
     * Every Relation has to have at least one Person of each type that is Primary
     *
     * @param Person $newPrimaryPerson
     * @param int $relationId
     * @param bool $primary
     * @return bool
     */
    private function switchPrimaryPerson(RelationsPerson $newPrimaryRP, bool $primary)
    {
        $rPersons = RelationsPerson::where([
            ['primary', 1],
            ['relation_id', $newPrimaryRP->relation_id]])->get();

        //If new relationsPerson WILL be primary, switch primary persons if there are any
        //We also don't know yet if our person was primary and thus part of the list
        if ($primary && $rPersons->count() > 0) {
            foreach ($rPersons as $rPerson) {
                //We don't want to switch the primary state of our new primary person
                if ($rPerson->person_id != $newPrimaryRP->person_id) {
                    $rPerson->primary = false;
                    $rPerson->save();
                }
            }
        }

        //If this is the last primary person of it's type, do not allow change to go through
        if (!$primary && $rPersons->count() <= 1) {
            return false;
        }

        return true;
    }

    /**
     * List persons of a relation
     *
     * @param int $relationId
     * @return Builder
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function listRelationsPersons(int $relationId): Builder
    {
        return \Querying::for(Person::class)
            ->enableFillableSelect()
            ->setFilter(request()->get('filter'))
            ->setSortable(request()->get('sort'))
            ->setSelectables(request()->get('select'))
            ->setSearch(request()->get('search'))
            ->defaultSort('-id')
            ->make()
            ->getQuery()
            ->whereHas('relations', function (Builder $query) use ($relationId) {
                $query->where('relation_id', $relationId);
            });
    }

    /**
     * Show relations_person
     *
     * @param $person_id
     * @param $relation_id
     * @param string $message
     * @param bool $code
     * @return RelationPersonResource
     */
    public function showRelationPerson($person_id, $relation_id, $message = '', $code = true)
    {
        return new RelationPersonResource(
            Person::find($person_id),
            RelationsPerson::where([['person_id', $person_id], ['relation_id', $relation_id]])->first(),
            $message,
            $code,
            true
        );
    }

    /**
     * Update relations_person
     *
     * @param array $data
     * @param Relation $relation
     * @param Person $person
     * @return array
     */
    public function updateRelationPerson(array $data, Relation $relation, Person $person)
    {
        // Update persons data
        $this->update($data, $person);

        // Update relations_persons data
        $relationsPersonAttributes = [
            'status' => $data['status'],
            'primary' => (key_exists('primary', $data) && $data['primary']),
            'person_type_id' => $data['person_type_id']
        ];
        $rp = RelationsPerson::where([
            ['relation_id', $relation->id],
            ['person_id', $person->id]])
            ->first();
        if ($data['primary']) {
            $allowed = $this->switchPrimaryPerson($rp, $relationsPersonAttributes['primary']);
            if (!$allowed) {
                return ['success' => false, 'data' => 'Not allowed to switch primary.'];
            }
        }
        $rp->update($relationsPersonAttributes);
        return [
            'success' => true,
            'data' => $this->showRelationPerson($rp->person_id, $rp->relation_id)
        ];
    }

    /**
     * Delete relations_person
     *
     * @param Relation $relation
     * @param Person $person
     * @return array
     */
    public function deleteRelationPerson(Relation $relation, Person $person)
    {
        $rp = RelationsPerson::where([['relation_id', $relation->id], ['person_id', $person->id]])->first();
        if (!$rp) {
            return [
            'success' => false,
            'message' => 'Person not found'
            ];
        }
        if ($rp->primary) {
            return [
            'success' => false,
            'message' => 'Cannot delete a primary Person'
            ];
        }

        Logging::information('Delete RelationsPerson', $rp, 1, 1);
        $rp->delete();
        if (RelationsPerson::where('person_id', $person->id)->count() === 0) {
            Logging::information('Delete Person', $person, 1, 1);
            $person->delete();
        }

        return [
            'success' => true,
            'data' => $person,
            'message' => 'Person updated successfully'
        ];
    }
}
