import React from "react";

import { GetRelations } from '../../controllers/relations';
import RelationForm from "./Form";
import ParentTable from '../../components/ParentTable';

const Relations = () => {
    return (
        <ParentTable
            table="Relations"
            data={GetRelations}
            columns={[
                {
                    dataField: "customer_number",
                    text: "Customer Number",
                    sort: true
                }, {
                    dataField: "company_name",
                    text: "Customer Name",
                    sort: true
                }, {
                    dataField: "email",
                    text: "Email",
                    sort: true
                }, {
                    dataField: "addresses[0].full_address", //TODO: Set address_type_id filtering = 1
                    text: "Address",
                    sort: false
                }, {
                    dataField: "persons[0].full_name", //TODO: Set person.primary=1 && person.active=1 filtering = 1
                    text: "Person",
                    sort: false
                }
            ]}
            action="link"
            form={RelationForm}
        />
    )
}

export default Relations;