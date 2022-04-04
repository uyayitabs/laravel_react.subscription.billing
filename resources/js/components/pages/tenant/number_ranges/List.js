import React from "react";

import { GetMyNumberRanges } from '../../../controllers/number_ranges';
import TenantForm from "./Form";
import ParentTable from '../../../components/ParentTable';

class NumberRanges extends React.Component {  
  constructor(props) {
    super(props)
  }
  render() {
    const {id} = this.props;
     return(
      <React.Fragment>
        <ParentTable
          table="NumberRanges"
          id={ id }
          data={ GetMyNumberRanges }
          columns={[
            {
              dataField: "sample_implementation",
              text: "Sample Implementation",
              sort: false
            },
            {
              dataField: "format",
              text: "Format",
              sort: true
            },
            {
              dataField: "description",
              text: "Description",
              sort: false
            },
            {
              dataField: "start",
              text: "Start",
              sort: false
            },
            {
              dataField: "end",
              text: "End",
              sort: false
            },
            {
              dataField: "randomized",
              text: "Randomized",
              sort: false,
              formatter: (cell, row) => cell === "1"
            },
            {
              dataField: "current",
              text: "Last saved number",
              sort: false
            },
          ]}
          action="edit"
          form={ TenantForm }
        />
      </React.Fragment>
    )
  }

}
export default NumberRanges;
