import React from 'react';

import { GetLatestSubscriptions } from '../../../controllers/dashboard';
import ParentTable from '../../../components/ParentTable';

const LatestSubscriptions = () => {
  return(
    <ParentTable
        table="Latest Subscriptions"
        data={ GetLatestSubscriptions }
        columns={[
            {
                dataField: "plan.description",
                text: "Plan",
                sort: false
            }, {
                dataField: "type",
                text: "Type",
                sort: false
            }, {
                dataField: "subscription_start",
                text: "Start",
                sort: false
            }, {
                dataField: "subscription_stop",
                text: "Stop",
                sort: false
            }
        ]}
        search={ false }
        pagination={ false }
      />
  )
}

export default LatestSubscriptions;
