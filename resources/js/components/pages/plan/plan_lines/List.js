import React from "react";

import { GetPlanLines } from '../../../controllers/plans';
import PlanLineForm from "./Form";
import ParentTable from '../../../components/ParentTable';

class PlanLines extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    const { id } = this.props;

    return (
      <ParentTable
        id={ id }
        table="Plan Lines"
        data={ GetPlanLines }
        columns={[{
            dataField: "product.description",
            text: "Product Type",
            sort: true
          }, {
            dataField: "plan_line_type.line_type",
            text: "Plan line type",
            sort: true
          }, {
            dataField: "plan_start",
            text: "Plan start",
            sort: true
          }, {
            dataField: "plan_stop",
            text: "Plan stop",
            sort: true
          }, {
            dataField: "plan_line_price.rounded_fixed_price",
            text: "Price",
            sort: true,
          }, {
            dataField: "plan_line_price.margin",
            text: "Margin",
            sort: true,
          }
        ]}
        action="link"
        form={ PlanLineForm }
      />
    )
  }
}

export default PlanLines;
