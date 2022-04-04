import React from "react";

import { GetInvoices } from '../../../controllers/relations';
// import SalesInvoiceForm from "../../invoice/Form";
import ParentTable from '../../../components/ParentTable';

class Invoices extends React.Component {
    constructor(props) {
        super(props)
    }

    render() {
        const { id } = this.props;

        return(
            <ParentTable
                id={ id }
                table="Invoices"
                data={ GetInvoices }
                columns={[
                    {
                        dataField: "invoice_no",
                        text: "Invoice Number",
                        sort: true
                    },
                    {
                        dataField: "date",
                        text: "Invoice Date",
                        sort: true
                    },
                    {
                        dataField: "relation_customer_number",
                        text: "Customer Number",
                        sort: true
                    },
                    {
                        dataField: "relation_company_name",
                        text: "Company Name",
                        sort: true
                    },
                    {
                        dataField: "relation_primary_address",
                        text: "Address",
                        sort: true
                    },
                    {
                        dataField: "relation_primary_person",
                        text: "Customer",
                        sort: true
                    },
                    {
                        dataField: "price",
                        text: "Price excl. VAT",
                        sort: true
                    },
                    {
                        dataField: "price_total",
                        text: "Price incl. VAT",
                        sort: true
                    },
                    {
                        dataField: "status",
                        text: "Status",
                        sort: false
                    }
                ]}
                action="link"
                // form={ SalesInvoiceForm }
                parent
            />
        )
    }  
}

export default Invoices;
