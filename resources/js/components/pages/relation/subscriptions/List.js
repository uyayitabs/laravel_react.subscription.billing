import React from "react";

import { GetSubscriptions } from '../../../controllers/relations';
import SubscriptionForm from "../../subscription/Form";
import ParentTable from '../../../components/ParentTable';

class Subscriptions extends React.Component {
  constructor(props) {
    super(props)
  }

  render() {
    const { id } = this.props;

    return(
      <ParentTable
        id={ id }
        table="Subscriptions"
        data={ GetSubscriptions }
        include="plan"
        columns={[
          {
            dataField: "description",
            text: "Description",
            sort: true
          },
          {
            dataField: "plan.description",
            text: "Plan",
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
            sort: false
          }
        ]}
        action="link"
        form={ SubscriptionForm }
        parent
      />
    )
  }
}

export default Subscriptions;
