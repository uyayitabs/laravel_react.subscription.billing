import React from "react";

import { GetGroups } from '../../../controllers/tenants';
import GroupForm from "./Form";
import ParentTable from '../../../components/ParentTable';

class Groups extends React.Component {
  constructor(props) {
    super(props)
  }
  render() {
    const { id } = this.props;
    return (
      <React.Fragment>
        <ParentTable
          table="Groups"
          id={id}
          data={GetGroups}
          columns={[
            {
              dataField: "name",
              text: "Name",
              sort: true
            },
            {
              dataField: "role",
              text: "Role",
              sort: false
            }
          ]}
          // action="edit"
          form={GroupForm}
        />
      </React.Fragment>
    )
  }
}

export default Groups;
