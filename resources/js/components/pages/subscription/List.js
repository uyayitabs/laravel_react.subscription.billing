import React from "react";

import { GetSubscriptions } from '../../controllers/subscriptions';
import SubscriptionForm from "./Form";
import ParentTable from '../../components/ParentTable';

const Subscriptions = () => {
  return(
    <ParentTable
        table="Subscriptions"
        data={ GetSubscriptions }
        include="plan,relation"
        columns={[
          {
            dataField: "relation.customer_number",
            text: "Customer Number",
            sort: false
          },
          {
            dataField: "description",
            text: "Description",
            sort: true
          },
          {
            dataField: "plan.description",
            text: "Plan Description",
            sort: false
          },
          {
            dataField: "subscription_start",
            text: "Start",
            sort: true
          },
          {
            dataField: "subscription_stop",
            text: "Stop",
            sort: true
          },
          {
            dataField: "costs",
            text: "Costs",
            sort: false,
            // formatter: (cell, row) => cell.toFixed(2)
          }
        ]}
        action="link"
        form={ SubscriptionForm }
      />
  )
}

export default Subscriptions;
