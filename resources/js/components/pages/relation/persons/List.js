import React from "react";

import { GetPersons } from '../../../controllers/relations';
import PersonForm from "./Form";
import ParentTable from '../../../components/ParentTable';

class Persons extends React.Component {
  constructor(props) {
    super(props);
  }

  render() {
    const { id } = this.props;

    return (
      <ParentTable
        id={ id }
        data={ GetPersons }
        include='user'
        columns={[
          {
            dataField: "first_name",
            text: "Firstname",
            sort: true
          },
          {
            dataField: "middle_name",
            text: "Middlename",
            sort: true
          },
          {
            dataField: "last_name",
            text: "Lastname",
            sort: true,
          },
          {
            dataField: "email",
            text: "E-mail",
            sort: true
          },
          {
            dataField: "phone",
            text: "Phone",
            sort: true
          },
          {
            dataField: "mobile",
            text: "Mobile",
            sort: true
          }
        ]}
        action="edit"
        form={ PersonForm }
      />
    )
  }
}

export default Persons;