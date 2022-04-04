import React from "react";

import { GetAddress } from '../../../controllers/relations';
import AddressForm from "./Form";
import ParentTable from '../../../components/ParentTable';

class Addresses extends React.Component {
  constructor(props) {
    super(props)
  }

  render() {
    const { id } = this.props;

    return (
      <ParentTable
        id={id}
        data={GetAddress}
        include="city,country,address-type"
        columns={[
          // {
          //   dataField: "address",
          //   text: "Address",
          //   sort: true
          // },
          {
            dataField: "address_type.type",
            text: "Type",
            sort: true
          }, {
            dataField: "street1",
            text: "Street 1",
            sort: true
          }, {
            dataField: "street2",
            text: "Street 2",
            sort: true
          }, {
            dataField: "house_number",
            text: "House number",
            sort: true
          }, {
            dataField: "room",
            text: "Room",
            sort: true
          }, {
            dataField: "zipcode",
            text: "Zipcode",
            sort: true
          }, {
            dataField: "country.name",
            text: "Country",
            sort: true
          }, {
            dataField: "city.name",
            text: "City",
            sort: true
          }
        ]}
        action="edit"
        form={AddressForm}
      />
    )
  }
}

export default Addresses;