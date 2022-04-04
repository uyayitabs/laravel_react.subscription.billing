import React from "react";

import { GetMyTenants } from '../../controllers/tenants';
import TenantForm from "./Form";
import ParentTable from '../../components/ParentTable';

const Tenants = () => {
  return(
    <ParentTable
        table="Tenants"
        data={ GetMyTenants }
        include='parent'
        columns={[
          {
            dataField: "name",
            text: "Tenant",
            sort: true
          },
          {
            dataField: "billing_day",
            text: "Invoice Billing Day",
            sort: true
          },
          {
            dataField: "parent.name",
            text: "Parent",
            sort: true
          }
        ]}
        action="link"
        form={ TenantForm }
      />
  )
}

export default Tenants;
