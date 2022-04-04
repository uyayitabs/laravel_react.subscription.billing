import React from "react";
import moment from 'moment';
import { GetPlanLinePrices } from '../../../../controllers/plans';
import PlanLinePricesForm from "./Form";
import ParentTable from '../../../../components/ParentTable';

class PlanLinePrices extends React.Component {
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
            table="Plan Line Prices"
            data={ GetPlanLinePrices }
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
            form={ PlanLinePricesForm }
        />
      </React.Fragment>
    )
  }
}

export default PlanLinePrices;
