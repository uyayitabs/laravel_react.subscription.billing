import React from "react";

import { GetPlans } from '../../controllers/plans';
import PlanForm from "./Form";
import ParentTable from '../../components/ParentTable';

const Plans = () => {
  return(
    <ParentTable
        table="Plans"
        data={ GetPlans }
        include='parent'
        columns={[
            {
                dataField: "description",
                text: "Description",
                sort: true
            },
            {
                dataField: "plan_start",
                text: "Start",
                sort: true
            },
            {
                dataField: "plan_stop",
                text: "Stop",
                sort: true
            },
            {
                dataField: "costs",
                text: "Costs",
                sort: true
            }
        ]}
        action="link"
        form={ PlanForm }
      />
  )
}

export default Plans;
