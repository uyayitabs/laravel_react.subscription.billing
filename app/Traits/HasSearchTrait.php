<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;

trait HasSearchTrait
{
    private function manageSearchKeyword($keyword)
    {
        if (!Str::contains($keyword, ':')) {
            return $keyword;
        }

        $pcs = explode(' ', $keyword);
        $keywords = [];
        foreach ($pcs as $i => $pc) {
            if (Str::contains($pc, ':')) {
                $keywords[] = $pc;
            } else {
                $keywords[count($keywords) - 1] = $keywords[count($keywords) - 1] . ' ' . $pc;
            }
        }

        return $keywords;
    }

    private function getTableName($tb = null)
    {
        if ('invoicePerson' == $tb) {
            return 'persons';
        }
        if ('lineType' == $tb) {
            return 'plan_subscription_line_types';
        }

        if ($tb) {
            $table = Str::plural(Str::snake($tb));
            if ($tb == 'person') {
                $table = 'persons';
            }
        } else {
            $table = $this->table;
        }

        return $table;
    }

    /**
     * Search Scope
     * @param $query, $keyword
     *
     * @return object|array data related models
     */
    public function scopeSearch($query, $keyword)
    {
        if (Str::contains($keyword, ':')) {
            $keywords = $this->manageSearchKeyword($keyword);
            foreach ($keywords as $keyword) {
                $searchCol = trim(Str::before($keyword, ':'));
                $searchKeyword = trim(trim(Str::after($keyword, ':')), '"');

                if ('vat_percentage' == $searchCol) {
                    $searchKeyword = Str::before($searchKeyword, '%');
                }

                foreach ($this->searchable as $searchable) {
                    if (Str::contains($searchable, $searchCol)) {
                        $tb = '';

                        if (Str::contains($searchable, '|')) {
                            $tb = Str::before($searchable, '|');
                            $cols = explode(',', Str::after($searchable, '|'));
                            $table = $this->getTableName($tb);
                            $query->whereHas($tb, function ($q1) use ($cols, $searchKeyword, $searchCol, $table, $tb) {
                                if ($tb == $searchCol) {
                                    if (isset($cols[0]) && "product" == $tb && 'name' == $cols[0]) {
                                        $col = 'description';
                                        $q1->where($table . '.' . $col, "=", $searchKeyword);
                                    } else {
                                        $q1->where($table . '.' . $cols[0], "=", $searchKeyword);
                                    }
                                } else {
                                    foreach ($cols as $col) {
                                        if (Str::contains($col, $searchCol)) {
                                            if (Str::contains($col, ':')) {
                                                $tb1 = Str::before($col, ':');
                                                $col1 = Str::after($col, ':');
                                                $table = $this->getTableName($tb1);
                                                $q1->whereHas($tb1, function ($q2) use ($col1, $searchKeyword, $table, $searchCol) {
                                                    if (Str::contains($col1, '.')) {
                                                        $cols = explode('.', $col1);
                                                        foreach ($cols as $col) {
                                                            if (Str::contains($col, $searchCol)) {
                                                                if (Str::contains($col, '!')) {
                                                                    $tb1 = Str::before($col, '!');
                                                                    $col1 = Str::after($col, '!');
                                                                    $table = $this->getTableName($tb1);
                                                                    $q2->whereHas($tb1, function ($q3) use ($col1, $searchKeyword, $table) {
                                                                        $q3->where($table . '.' . $col1, "=", $searchKeyword);
                                                                    });
                                                                } else {
                                                                    $q2->where($table . '.' . $col, "=", $searchKeyword);
                                                                }
                                                            }
                                                        }
                                                    } else {
                                                        $q2->where($table . '.' . $col1, "=", $searchKeyword);
                                                    }
                                                });
                                            } else {
                                                $q1->where($table . '.' . $col, "=", $searchKeyword);
                                            }
                                            break;
                                        }
                                    }
                                }
                            });
                        } else {
                            $cols = explode(',', $searchable);
                            foreach ($cols as $col) {
                                if (Str::contains($col, $searchCol)) {
                                    if (Str::contains($col, ':')) {
                                        $tb1 = Str::before($col, ':');
                                        $col1 = Str::after($col, ':');
                                        $table = $this->getTableName($tb1);
                                        $query->whereHas($tb1, function ($q1) use ($col1, $searchKeyword, $table) {
                                            $q1->where($table . '.' . $col1, "=", $searchKeyword);
                                        });
                                    } else {
                                        if ('invoice_date' == $col) {
                                            $col = 'date';
                                        }
                                        if (
                                            in_array($col, [
                                            'date',
                                            'due_date',
                                            'subscription_start',
                                            'subscription_stop',
                                            'active_from',
                                            'active_to',
                                            'date_from',
                                            'date_to',
                                            'plan_start',
                                            'plan_stop'
                                            ])
                                        ) {
                                            $searchKeyword = dateFormat($searchKeyword);
                                        }

                                        $table = $this->table;
                                        if ('vat_percentage' == $col) {
                                            $searchKeyword = $searchKeyword / 100;
                                        }
                                        $query->where($table . '.' . $col, "=", $searchKeyword);
                                    }
                                    break;
                                }
                            }
                        }
                        break;
                    }
                }
            }

            return $query;
        }

        $keywords = explode(',', $keyword);

        if (count($keywords)) {
            foreach ($keywords as $i => $k) {
                $key = explode(' ', $k);

                foreach ($key as $n => $ky) {
                    if ($i == 0 && $n == 0) {
                        $query->where(function ($query) use ($ky) {
                            $this->searchBuilder($query, $ky);
                        });
                    } else {
                        if ($n == 0) {
                            $query->orWhere(function ($query) use ($ky) {
                                $this->searchBuilder($query, $ky);
                            });
                        } else {
                            $query->where(function ($query) use ($ky) {
                                $this->searchBuilder($query, $ky);
                            });
                        }
                    }
                }
            }
        } else {
            $this->searchBuilder($query, $keyword);
        }

        return $query;
    }

    private function searchBuilder($query, $keyword)
    {
        foreach ($this->fillable as $searchable) {
            $pos = strrpos($searchable, "|");

            $tb = '';
            $cols = [];

            if ($pos !== false) {
                $pcs = explode('|', $searchable);
                $tb = $pcs[0];
                $cols = explode(',', $pcs[1]);
            } else {
                $cols = explode(',', $searchable);
            }

            if ($tb != '') {
                $table = $this->getTableName($tb);
                $query->orWhereHas($tb, function ($query1) use ($cols, $keyword, $table) {
                    foreach ($cols as $i => $col) {
                        $childPos = strrpos($col, ":");
                        if ('invoice_date' == $col) {
                            $col = 'date';
                        }
                        if ('products' == $table && "name" == $col) {
                            $col = 'description';
                        }
                        if ($childPos !== false) {
                            $childPcs = explode(':', $col);
                            $childTb = $childPcs[0];
                            $childCols = explode('.', $childPcs[1]);
                            $table = $this->getTableName($childTb);
                            $query1->orWhereHas($childTb, function ($query2) use ($childCols, $keyword, $table) {
                                foreach ($childCols as $j => $col) {
                                    if ('invoice_date' == $col) {
                                        $col = 'date';
                                    }
                                    if (Str::contains($col, '!')) {
                                        $tb1 = Str::before($col, '!');
                                        $col1 = Str::after($col, '!');
                                        $table = $this->getTableName($tb1);
                                        $query2->orWhereHas($tb1, function ($q3) use ($col1, $keyword, $table) {
                                            $q3->where($table . '.' . $col1, 'LIKE', '%' . $keyword . '%');
                                        });
                                    } else {
                                        $query2->where($table . '.' . $col, 'LIKE', '%' . $keyword . '%');
                                    }
                                }
                            });
                        } else {
                            if ($i == 0) {
                                $query1->where($table . '.' . $col, 'LIKE', '%' . $keyword . '%');
                            } else {
                                $query1->orWhere($table . '.' . $col, 'LIKE', '%' . $keyword . '%');
                            }
                        }
                    }
                });
            } else {
                $table = $this->getTableName();
                foreach ($cols as $ii => $col) {
                    if ('invoice_date' == $col) {
                        $col = 'date';
                    }
                    if ($this->table == 'subscriptions') {
                        if ('stop' == $col) {
                            $col = 'subscription_stop';
                        }
                        if ('start' == $col) {
                            $col = 'subscription_start';
                        }
                    }

                    // commented if else statement generates AND instead of OR conditioning
                    // if ($ii == 0) {
                    //    $query->where($table . '.' . $col, 'LIKE', '%' . $keyword . '%');
                    //} else {
                    //    $query->orWhere($table . '.' . $col, 'LIKE', '%' . $keyword . '%');
                    //}
                    $query->orWhere($table . '.' . $col, 'LIKE', '%' . $keyword . '%');
                }
            }
        }
    }
}
