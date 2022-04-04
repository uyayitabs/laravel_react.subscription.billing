import React from "react";

import { GetUsers } from '../../controllers/users';
import UserForm from "./Form";
// import PersonForm from "../relation/persons/Form";
import ParentTable from '../../components/ParentTable';

const Users = () => {
  return(
    <ParentTable
        table="Users"
        data={ GetUsers }
        include='person'
        columns={[
          {
            dataField: "username",
            text: "Username",
            sort: true,
            disabled: true
          },
          {
            dataField: "person.email",
            text: "Email",
            sort: true
          },
          {
            dataField: "role",
            text: "Role",
            type: "select",
            sort: true
          }
        ]}
        action="edit"
        form={ UserForm }
      />
  )
}

export default Users;
