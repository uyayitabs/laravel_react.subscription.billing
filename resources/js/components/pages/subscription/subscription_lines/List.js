import React from "react";

import { GetSubscriptionLines } from '../../../controllers/subscriptions';
import SubscriptionLineForm from "./Form";
import ParentTable from '../../../components/ParentTable';

class SubscriptionLines extends React.Component {
  constructor(props) {
    super(props)
  }

  render() {
    const { id } = this.props;

    return (
      <React.Fragment>
        <ParentTable
          id={ id }
          table="Subscription Lines"
          data={ GetSubscriptionLines }
          include="product"
          columns={[{
              dataField: "product.description",
              text: "Product",
              sort: true
          }, {
              dataField: "line_type.line_type",
              text: "Line Type",
              sort: true
          }, {
              dataField: "description",
              text: "Description",
              sort: true
          }, {
              dataField: "subscription_line_price.price_valid_from",
              text: "Price Valid",
              sort: true
          }, {
              dataField: "subscription_line_price.rounded_fixed_price",
              text: "Fixed Price",
              sort: true
          }, {
              dataField: "subscription_line_price.margin",
              text: "Margin",
              sort: true
          }, {
              dataField: "subscription_start",
              text: "Start",
              sort: true
          }, {
              dataField: "subscription_stop",
              text: "Stop",
              sort: true
          }]}
          action="link"
          form={ SubscriptionLineForm }
        />
      </React.Fragment>
    )
  }
}

export default SubscriptionLines;