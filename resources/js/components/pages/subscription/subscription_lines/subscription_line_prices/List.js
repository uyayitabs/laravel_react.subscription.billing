import React from "react";

import { GetSubscriptionLinePrices } from '../../../../controllers/subscriptions';
import SubscriptionLinePricesForm from "./Form";
import ParentTable from '../../../../components/ParentTable';

class SubscriptionLinePrices extends React.Component {
  constructor(props) {
    super(props)
  }

  render() {
    const { id } = this.props;
    const { id2 } = this.props;

    return (
      <React.Fragment>
        <ParentTable
            id={ id }
            id2= { id2 }
            table="Subscription Lines"
            data={ GetSubscriptionLinePrices }
            columns={[
            {
                dataField: "rounded_fixed_price",
                text: "Fixed Price",
                sort: true
            }, {
                dataField: "margin",
                text: "Margin",
                sort: true
            }, {
                dataField: "price_valid_from",
                text: "Valid from",
                formatter: this.dateFormatter
            }]}
            action="edit"
            form={ SubscriptionLinePricesForm }
        />
      </React.Fragment>
    )
  }
}

export default SubscriptionLinePrices;